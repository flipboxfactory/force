<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\models;

use craft\base\Model;
use flipbox\force\services\Cache;
use flipbox\force\services\Connections;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Settings extends Model
{
    /**
     * @var string
     */
    public $sObjectAssociationTablePostfix = '';

    /**
     * @var string
     */
    private $defaultCache = Cache::APP_CACHE;

    /**
     * @var string
     */
    private $defaultConnection = Connections::APP_CONNECTION;

    /**
     * @var string
     */
    public $instanceUrl = '';

    /**
     * @var string
     */
    public $sObjectViewUrlString = '{{ instanceUrl }}/lightning/o/{{ sObject }}/{{ id }}/view';

    /**
     * @var string
     */
    public $sObjectListUrlString = '{{ instanceUrl }}/lightning/o/{{ sObject }}/list';


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
}
