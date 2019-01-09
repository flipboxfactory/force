<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\records;

use Craft;
use flipbox\craft\integration\records\IntegrationAssociation;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use flipbox\force\migrations\ObjectAssociations;
use flipbox\force\criteria\ObjectAccessorCriteria;

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
        $this->ensureTableExists();
        parent::__construct($config);
    }


    /**
     * @throws \Throwable
     */
    public function ensureTableExists()
    {
        if (!in_array(
            Craft::$app->getDb()->tablePrefix . static::tableAlias(),
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
        (new ObjectAssociations())->up();
        ob_end_clean();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableAlias()
    {
        return parent::tableAlias() . Force::getInstance()->getSettings()->environmentTablePostfix;
    }

    /**
     * @param array $criteria
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
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

        $resource = new ObjectAccessorCriteria([
            'connection' => $field->getConnection(),
            'cache' => $field->getCache()
        ]);

        // Can't override these...
        $criteria['id'] = $this->objectId ?: self::DEFAULT_ID;
        $criteria['object'] = $field->object;

        return $resource->read($criteria);
    }
}
