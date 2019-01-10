<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\events;

use Psr\SimpleCache\CacheInterface;
use yii\base\Event;

class RegisterCacheEvent extends Event
{
    /**
     * @var array|CacheInterface[]
     */
    public $cache = [];
}
