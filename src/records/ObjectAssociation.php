<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\records;

use Craft;
use flipbox\craft\integration\records\IntegrationAssociation;
use flipbox\craft\sortable\associations\records\SortableAssociation;
use flipbox\craft\sortable\associations\services\SortableAssociations;
use flipbox\ember\helpers\ModelHelper;
use flipbox\ember\records\traits\ElementAttribute;
use flipbox\ember\records\traits\SiteAttribute;
use flipbox\force\db\ObjectAssociationQuery;
use flipbox\force\fields\Objects;
use flipbox\force\Force;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property int $fieldId
 * @property string $objectId
 */
class ObjectAssociation extends IntegrationAssociation
{
    /**
     * @inheritdoc
     */
    const TABLE_ALIAS = 'salesforce_object_associations';

    /**
     * @inheritdoc
     * @throws \Throwable
     */
    public function __construct(array $config = [])
    {
        Force::getInstance()->getObjectAssociations()->ensureTableExists();
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableAlias()
    {
        return parent::tableAlias() . Force::getInstance()->getSettings()->environmentTablePostfix;
    }

    /**
     * @return SortableAssociations
     */
    protected function associationService(): SortableAssociations
    {
        return Force::getInstance()->getObjectAssociations();
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return ObjectAssociationQuery
     */
    public static function find(): ObjectAssociationQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        /** @noinspection PhpUnhandledExceptionInspection */
        return Craft::createObject(ObjectAssociationQuery::class, [get_called_class()]);
    }

    /**
     * @param array $criteria
     * @return mixed|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getObject(array $criteria = [])
    {
        if (null === ($field = $this->getField())) {
            return null;
        }

        if (!$field instanceof Objects) {
            return null;
        }

        $base = [
            'connection' => $field->getConnection(),
            'cache' => $field->getCache()
        ];

        $resource = Force::getInstance()->getResources()->getObject();

        // Can't override these...
        $criteria['id'] = $this->{self::TARGET_ATTRIBUTE} ?: self::DEFAULT_ID;
        $criteria['object'] = $field->object;

        return $resource->read(
            $resource->getAccessorCriteria(
                array_merge(
                    $base,
                    $criteria
                )
            )
        );
    }
}
