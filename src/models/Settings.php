<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\models;

use craft\base\Model;
use flipbox\craft\ember\helpers\ModelHelper;
use flipbox\craft\salesforce\services\Cache;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Settings extends Model
{
    const DEFAULT_CONNECTION = 'salesforcez';

    /**
     * @var string
     */
    public $environmentTablePostfix = '';

    /**
     * @var string
     */
    private $defaultCache = Cache::APP_CACHE;

    /**
     * @var string
     */
    private $defaultConnection = self::DEFAULT_CONNECTION;

    /**
     * @param string $key
     * @return $this
     */
    public function setDefaultConnection(string $key)
    {
        $this->defaultConnection = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultConnection(): string
    {
        return $this->defaultConnection;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setDefaultCache(string $key)
    {
        $this->defaultCache = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultCache(): string
    {
        return $this->defaultCache;
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            [
                'defaultConnection',
                'defaultCache'
            ]
        );
    }

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    [
                        'defaultConnection',
                        'defaultCache'
                    ],
                    'safe',
                    'on' => [
                        ModelHelper::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );
    }
}
