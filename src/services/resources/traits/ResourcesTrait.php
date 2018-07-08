<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services\resources\traits;

use flipbox\force\connections\ConnectionInterface;
use flipbox\force\criteria\InstanceCriteriaInterface;
use flipbox\force\Force;
use flipbox\force\helpers\CacheHelper;
use flipbox\force\helpers\ConnectionHelper;
use flipbox\force\helpers\TransformerHelper;
use flipbox\force\pipeline\Resource;
use flipbox\force\transformers\collections\TransformerCollectionInterface;
use Flipbox\Relay\Builder\RelayBuilderInterface;
use Flipbox\Relay\Salesforce\Builder\Resources\Resources;
use League\Pipeline\PipelineBuilderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait ResourcesTrait
{
    /**
     * @return array|TransformerCollectionInterface
     */
    public abstract static function defaultTransformer();

    /**
     * @param InstanceCriteriaInterface $criteria
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function resources(
        InstanceCriteriaInterface $criteria,
        $source = null
    ) {
        return $this->rawResources(
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $source
        );
    }

    /**
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawResources(
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawResourcesPipeline(
            $connection,
            $cache,
            $transformer
        )($source);
    }

    /**
     * @param InstanceCriteriaInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function resourcesPipeline(
        InstanceCriteriaInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawResourcesPipeline(
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }

    /**
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawResourcesPipeline(
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer, static::defaultTransformer()),
            [
                'resource' => [Resources::class]
            ]
        );

        return (new Resource(
            $this->rawHttpResourcesRelay(
                ConnectionHelper::resolveConnection($connection),
                $cache
            ),
            $transformer,
            Force::getInstance()->getPsrLogger()
        ));
    }


    /**
     * @param InstanceCriteriaInterface $criteria
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpResourcesRelay(
        InstanceCriteriaInterface $criteria
    ): callable {
        return $this->rawHttpResourcesRelay(
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpResourcesRelay(
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        $connection = ConnectionHelper::resolveConnection($connection);

        /** @var RelayBuilderInterface $builder */
        $builder = new Resources(
            $connection,
            $connection,
            CacheHelper::resolveCache($cache),
            Force::getInstance()->getPsrLogger()
        );

        return $builder->build();
    }

    /**
     * @param InstanceCriteriaInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpResources(
        InstanceCriteriaInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpResources(
            $criteria->getConnection(),
            $criteria->getCache()
        )();
    }

    /**
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpResources(
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpResourcesRelay(
            $connection,
            $cache
        )();
    }
}
