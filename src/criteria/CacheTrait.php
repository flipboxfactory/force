<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\criteria;

use flipbox\craft\salesforce\Force;
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
    protected $cache = 'default';

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
        return $this->cache = $this->resolveCache($this->cache);
    }

    /**
     * @param null|string|CacheInterface $cache
     * @return CacheInterface
     * @throws \yii\base\InvalidConfigException
     */
    protected function resolveCache($cache): CacheInterface
    {
        if ($cache instanceof CacheInterface) {
            return $cache;
        }

        if ($cache === null) {
            $cache = Force::getInstance()->getSettings()->getDefaultCache();
        }

        return Force::getInstance()->getCache()->get($cache);
    }
}
