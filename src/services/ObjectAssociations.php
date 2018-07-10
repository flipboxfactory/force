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
use flipbox\force\criteria\ObjectAccessorCriteria;
use flipbox\force\db\ObjectAssociationQuery;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use flipbox\force\migrations\ObjectAssociations as ObjectAssociationsMigration;
use flipbox\force\records\ObjectAssociation;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 *
 * @method ObjectAssociationQuery parentGetQuery($config = [])
 * @method ObjectAssociation create(array $attributes = [])
 * @method ObjectAssociation find($identifier)
 * @method ObjectAssociation get($identifier)
 * @method ObjectAssociation findByCondition($condition = [])
 * @method ObjectAssociation getByCondition($condition = [])
 * @method ObjectAssociation findByCriteria($criteria = [])
 * @method ObjectAssociation getByCriteria($criteria = [])
 * @method ObjectAssociation[] findAllByCondition($condition = [])
 * @method ObjectAssociation[] getAllByCondition($condition = [])
 * @method ObjectAssociation[] findAllByCriteria($criteria = [])
 * @method ObjectAssociation[] getAllByCriteria($criteria = [])
 */
class ObjectAssociations extends SortableAssociations
{
    use Accessor {
        getQuery as parentGetQuery;
    }

    /**
     * @inheritdoc
     */
    const SOURCE_ATTRIBUTE = ObjectAssociation::SOURCE_ATTRIBUTE;

    /**
     * @inheritdoc
     */
    const TARGET_ATTRIBUTE = ObjectAssociation::TARGET_ATTRIBUTE;

    /**
     * @inheritdoc
     */
    protected static function tableAlias(): string
    {
        return ObjectAssociation::tableAlias();
    }

    /**
     * @inheritdoc
     */
    public static function recordClass(): string
    {
        return ObjectAssociation::class;
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
        (new ObjectAssociationsMigration())->up();
        ob_end_clean();

        return true;
    }

    /**
     * @inheritdoc
     * @return ObjectAssociationQuery
     */
    public function getQuery($config = []): SortableAssociationQueryInterface
    {
        return $this->parentGetQuery($config);
    }

    /**
     * @inheritdoc
     * @param ObjectAssociation $record
     * @return ObjectAssociationQuery
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
     * @param ObjectAssociationQuery $query
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
     * @param ObjectAssociation $record
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function validateObject(
        ObjectAssociation $record
    ): bool {

        if (null === ($fieldId = $record->fieldId)) {
            return false;
        }

        if (null === ($field = Force::getInstance()->getObjectsField()->findById($fieldId))) {
            return false;
        }

        $criteria = new ObjectAccessorCriteria(
            [
                'object' => $field->object,
                'id' => $record->objectId
            ]
        );

        /** @var ResponseInterface $response */
        $response = Force::getInstance()->getResources()->getObject()->httpRead(
            $criteria
        );

        return $response->getStatusCode() >= 200 && $response->getStatusCode() <= 299;
    }

    /**
     * @param $source
     * @param int $fieldId
     * @param int $siteId
     * @return ObjectAssociationQuery
     */
    private function query(
        $source,
        int $fieldId,
        int $siteId
    ): ObjectAssociationQuery {
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
        if (null === ($fieldId = $this->resolveStringAttribute($query, 'field'))) {
            return null;
        }

        return Force::getInstance()->getObjectsField()->findById($fieldId);
    }
}
