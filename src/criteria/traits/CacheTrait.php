<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\criteria\traits;

use flipbox\force\helpers\CacheHelper;
use flipbox\force\services\Cache;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait CacheTrait
{
    /**
     * @var CacheInterface|string|null
     */
    protected $cache = Cache::DEFAULT_CACHE;

    /**
     * @param $value
     * @return $this
     */
    public function cache($value)
    {
        return $this->setCache($value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setCache($value)
    {
        $this->cache = $value;
        return $this;
    }

    /**
     * @return CacheInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function getCache(): CacheInterface
    {
        return $this->cache = CacheHelper::resolveCache($this->cache);
    }
}
