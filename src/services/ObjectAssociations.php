<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services;

use Craft;
use flipbox\craft\integration\records\IntegrationAssociation;
use flipbox\craft\integration\services\IntegrationAssociations;
use flipbox\craft\integration\services\IntegrationField;
use flipbox\force\criteria\ObjectAccessorCriteria;
use flipbox\force\db\ObjectAssociationQuery;
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
class ObjectAssociations extends IntegrationAssociations
{
    /**
     * @inheritdoc
     * @throws \Throwable
     */
    public function init()
    {
        $settings = Force::getInstance()->getSettings();
        $this->cacheDuration = $settings->associationsCacheDuration;
        $this->cacheDependency = $settings->associationsCacheDependency;

        parent::init();

        $this->ensureTableExists();
    }

    /**
     * @inheritdoc
     * @return ObjectsField
     */
    protected function fieldService(): IntegrationField
    {
        return Force::getInstance()->getObjectsField();
    }

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
     * @param IntegrationAssociation $record
     */
    public function validateObject(
        IntegrationAssociation $record
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
}
