<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services;

use Craft;
use craft\helpers\Json;
use flipbox\craft\sortable\associations\db\SortableAssociationQueryInterface;
use flipbox\craft\sortable\associations\records\SortableAssociationInterface;
use flipbox\craft\sortable\associations\services\SortableAssociations;
use flipbox\ember\services\traits\records\Accessor;
use flipbox\ember\validators\MinMaxValidator;
use flipbox\force\db\SObjectAssociationQuery;
use flipbox\force\db\SObjectFieldQuery;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use flipbox\force\migrations\SObjectAssociations as SObjectAssociationsMigration;
use flipbox\force\records\SObjectAssociation;
use flipbox\force\transformers\collections\TransformerCollection;
use yii\base\DynamicModel;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 *
 * @method SObjectAssociationQuery parentGetQuery($config = [])
 * @method SObjectAssociation create(array $attributes = [])
 * @method SObjectAssociation find($identifier)
 * @method SObjectAssociation get($identifier)
 * @method SObjectAssociation findByCondition($condition = [])
 * @method SObjectAssociation getByCondition($condition = [])
 * @method SObjectAssociation findByCriteria($criteria = [])
 * @method SObjectAssociation getByCriteria($criteria = [])
 * @method SObjectAssociation[] findAllByCondition($condition = [])
 * @method SObjectAssociation[] getAllByCondition($condition = [])
 * @method SObjectAssociation[] findAllByCriteria($criteria = [])
 * @method SObjectAssociation[] getAllByCriteria($criteria = [])
 */
class ObjectAssociations extends SortableAssociations
{
    use Accessor {
        getQuery as parentGetQuery;
    }

    /**
     * @inheritdoc
     */
    const SOURCE_ATTRIBUTE = SObjectAssociation::SOURCE_ATTRIBUTE;

    /**
     * @inheritdoc
     */
    const TARGET_ATTRIBUTE = SObjectAssociation::TARGET_ATTRIBUTE;

    /**
     * @inheritdoc
     */
    protected static function tableAlias(): string
    {
        return SObjectAssociation::tableAlias();
    }

    /**
     * @inheritdoc
     */
    public static function recordClass(): string
    {
        return SObjectAssociation::class;
    }

    /**
     * @throws \Throwable
     */
    public function ensureTableExists()
    {
        if (!in_array(
            Craft::$app->getDb()->tablePrefix . self::tableAlias(),
            Craft::$app->getDb()->getSchema()->tableNames,
            true
        )) {
            $this->createTable();
        }
    }

    /**
     * @return bool
     * @throws \Throwable
     */
    private function createTable(): bool
    {
        ob_start();
        (new SObjectAssociationsMigration())->up();
        ob_end_clean();

        return true;
    }

    /**
     * @inheritdoc
     * @return SObjectAssociationQuery
     */
    public function getQuery($config = []): SortableAssociationQueryInterface
    {
        return $this->parentGetQuery($config);
    }

    /**
     * @inheritdoc
     * @param SObjectAssociation $record
     * @return SObjectAssociationQuery
     */
    protected function associationQuery(
        SortableAssociationInterface $record
    ): SortableAssociationQueryInterface {
        return $this->query(
            $record->{static::SOURCE_ATTRIBUTE},
            $record->fieldId,
            $record->siteId
        );
    }

    /**
     * @inheritdoc
     * @param SObjectAssociationQuery $query
     */
    protected function existingAssociations(
        SortableAssociationQueryInterface $query
    ): array {
        $source = $this->resolveStringAttribute($query, 'element');
        $field = $this->resolveStringAttribute($query, 'field');
        $site = $this->resolveStringAttribute($query, 'siteId');

        if ($source === null || $field === null || $site === null) {
            return [];
        }

        return $this->associations($source, $field, $site);
    }

    /**
     * @param SObjectAssociation $record
     * @return bool
     * @throws \Exception
     */
    public function validateSObject(
        SObjectAssociation $record
    ): bool {

        if (null === ($fieldId = $record->fieldId)) {
            return false;
        }

        if (null === ($field = Force::getInstance()->getObjectsField()->findById($fieldId))) {
            return false;
        }

        $criteria = $field->createCriteria([
            'id' => $record->sObjectId
        ]);

        /** @var DynamicModel $response */
        $response = $criteria->get(['transformer' => TransformerCollection::class], $record);

        return !$response->hasErrors();
    }

    /**
     * @param $source
     * @param int $fieldId
     * @param int $siteId
     * @return SObjectAssociationQuery
     */
    private function query(
        $source,
        int $fieldId,
        int $siteId
    ): SObjectAssociationQuery {
        return $this->getQuery()
            ->where([
                static::SOURCE_ATTRIBUTE => $source,
                'fieldId' => $fieldId,
                'siteId' => $siteId
            ])
            ->orderBy(['sortOrder' => SORT_ASC]);
    }

    /**
     * @param $source
     * @param int $fieldId
     * @param int $siteId
     * @return array
     */
    private function associations(
        $source,
        int $fieldId,
        int $siteId
    ): array {
        return $this->query($source, $fieldId, $siteId)
            ->indexBy(static::TARGET_ATTRIBUTE)
            ->all();
    }

    /**
     * @inheritdoc
     * @param bool $validate
     * @throws \Exception
     */
    public function save(
        SortableAssociationQueryInterface $query,
        bool $validate = true
    ): bool {
        if ($validate === true && null !== ($field = $this->resolveFieldFromQuery($query))) {
            $error = '';

            (new MinMaxValidator([
                'min' => $field->min,
                'max' => $field->max
            ]))->validate($query, $error);

            if (!empty($error)) {
                Force::error(sprintf(
                    "Domains failed to save due to the following validation errors: '%s'",
                    Json::encode($error)
                ));
                return false;
            }
        }

        return parent::save($query);
    }

    /**
     * @param SortableAssociationQueryInterface $query
     * @return Objects|null
     */
    protected function resolveFieldFromQuery(
        SortableAssociationQueryInterface $query
    ) {
        if ($query instanceof SObjectFieldQuery) {
            return $query->getField();
        }

        if (null === ($fieldId = $this->resolveStringAttribute($query, 'field'))) {
            return null;
        }

        return Force::getInstance()->getObjectsField()->findById($fieldId);
    }
}
