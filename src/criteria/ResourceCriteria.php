<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\criteria;

use flipbox\ember\helpers\ObjectHelper;
use flipbox\force\Force;
use flipbox\force\helpers\TransformerHelper;
use flipbox\force\services\Cache;
use flipbox\force\services\Connections;
use flipbox\force\transformers\collections\DynamicTransformerCollection;
use Flipbox\Salesforce\Connections\ConnectionInterface;
use Flipbox\Salesforce\Transformers\Collections\TransformerCollectionInterface;
use Psr\SimpleCache\CacheInterface;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ResourceCriteria extends BaseObject implements CriteriaInterface
{
    /**
     * @var ConnectionInterface|string
     */
    protected $connection = Connections::DEFAULT_CONNECTION;

    /**
     * @var CacheInterface|string|null
     */
    protected $cache = Cache::DEFAULT_CACHE;

    /**
     * @var TransformerCollectionInterface|null
     */
    protected $transformer = ['class' => DynamicTransformerCollection::class];

    /**
     * @param $value
     * @return $this
     */
    public function connection($value)
    {
        return $this->setConnection($value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setConnection($value)
    {
        $this->connection = $value;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getConnection(): ConnectionInterface
    {
        if ($this->connection instanceof ConnectionInterface) {
            return $this->connection;
        }

        if ($this->connection === null) {
            $this->connection = Connections::DEFAULT_CONNECTION;
        }

        return $this->connection = Force::getInstance()->getConnections()->get($this->connection);
    }

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
     * @inheritdoc
     */
    public function getCache(): CacheInterface
    {
        if ($this->cache instanceof CacheInterface) {
            return $this->cache;
        }

        if ($this->cache === null) {
            $this->cache = Cache::DEFAULT_CACHE;
        }

        return $this->cache = Force::getInstance()->getCache()->get($this->cache);
    }

    /**
     * @param $value
     * @return $this
     */
    public function transformer($value)
    {
        return $this->setTransformer($value);
    }

    /**
     * @inheritdoc
     */
    public function setTransformer($value)
    {
        if(empty($value)) {
            $this->transformer = null;
            return $this;
        }

        if (is_string($value)) {
            if (TransformerHelper::isTransformerCollectionClass($value)) {
                $value = ['class' => $value];
            } else {
                $value = ['handle' => [$value]];
            }
        }

        if (array_key_exists('class', $value)) {
            $this->transformer = $value;
            return $this;
        }

        TransformerHelper::populateTransformerCollection(
            $this->getTransformer(),
            $value
        );

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTransformer()
    {
        if ($this->transformer === false) {
            return null;
        }

        if ($this->transformer instanceof TransformerCollectionInterface) {
            return $this->transformer;
        }

        // Prevent subsequent resolves (since it already didn't)
        if (null === ($this->transformer = TransformerHelper::resolveCollection($this->transformer))) {
            $this->transformer = false;
            return null;
        }

        return $this->transformer;
    }

    /**
     * @inheritdoc
     */
    protected function prepare(array $criteria = [])
    {
        ObjectHelper::populate(
            $this,
            $criteria
        );
    }
}
