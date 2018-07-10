<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services;

use craft\helpers\Json;
use flipbox\ember\helpers\ArrayHelper;
use flipbox\ember\services\traits\objects\AccessorByString;
use flipbox\ember\services\traits\records\ActiveRecord;
use flipbox\force\criteria\c;
use flipbox\force\criteria\QueryCriteria;
use flipbox\force\criteria\QueryCriteriaInterface;
use flipbox\force\records\Query as QueryRecord;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method QueryCriteria create($config = [])
 * @method QueryCriteria find($identifier)
 * @method QueryCriteria get($identifier)
 * @method QueryCriteria findByString($identifier)
 * @method QueryCriteria getByString($identifier)
 * @method QueryCriteria findByCondition($condition = [])
 * @method QueryCriteria getByCondition($condition = [])
 * @method QueryCriteria findByCriteria($criteria = [])
 * @method QueryCriteria getByCriteria($criteria = [])
 * @method QueryCriteria[] findAllByCondition($condition = [])
 * @method QueryCriteria[] getAllByCondition($condition = [])
 * @method QueryCriteria[] findAllByCriteria($criteria = [])
 * @method QueryCriteria[] getAllByCriteria($criteria = [])
 */
class Queries extends Component
{
    use AccessorByString, ActiveRecord {
        prepareConfig as parentPrepareConfig;
        AccessorByString::create insteadof ActiveRecord;
        ActiveRecord::getDb insteadof AccessorByString;
        ActiveRecord::getQuery insteadof AccessorByString;
    }

    /**
     * @inheritdoc
     */
    public static function objectClass()
    {
        return QueryCriteria::class;
    }

    /**
     * @return string
     */
    public static function objectClassInstance()
    {
        return QueryCriteriaInterface::class;
    }

    /**
     * @inheritdoc
     */
    protected static function stringProperty(): string
    {
        return 'handle';
    }

    /**
     * @inheritdoc
     */
    public static function recordClass(): string
    {
        return QueryRecord::class;
    }


    /**
     * @inheritdoc
     */
    protected function prepareQueryConfig($config = [])
    {
        if (!is_array($config)) {
            $config = ArrayHelper::toArray($config, [], false);
        }

        if (!array_key_exists('select', $config)) {
            $config['select'] = ['settings'];
        }

        $config['asArray'] = true;

        return $config;
    }

    /**
     * @inheritdoc
     */
    protected function prepareConfig(array $config = []): array
    {
        $config = $this->parentPrepareConfig($config);
        return $this->prepareConfigSettings($config);
    }

    /**
     * @param array $config
     * @return array
     */
    private function prepareConfigSettings(array $config = []): array
    {
        // Handle settings
        $settings = ArrayHelper::remove($config, 'settings');

        if (is_string($settings)) {
            $settings = Json::decodeIfJson($settings);
        }

        if (!is_array($settings) || empty($settings)) {
            return $config;
        }

        return array_merge($config, $settings);
    }
}
