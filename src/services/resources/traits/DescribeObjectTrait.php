<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services\resources\traits;

use flipbox\force\connections\ConnectionInterface;
use flipbox\force\criteria\ObjectAccessorCriteriaInterface;
use flipbox\force\Force;
use flipbox\force\helpers\CacheHelper;
use flipbox\force\helpers\ConnectionHelper;
use flipbox\force\helpers\TransformerHelper;
use flipbox\force\pipeline\Resource;
use flipbox\force\transformers\collections\TransformerCollectionInterface;
use Flipbox\Relay\Builder\RelayBuilderInterface;
use Flipbox\Relay\Salesforce\Builder\Resources\SObject\Describe;
use League\Pipeline\PipelineBuilderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait DescribeObjectTrait
{
    /**
     * @return array|TransformerCollectionInterface
     */
    public abstract static function defaultTransformer();

    /**
     * @param ObjectAccessorCriteriaInterface $criteria
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function describe(
        ObjectAccessorCriteriaInterface $criteria,
        array $extra = []
    ) {
        return $this->rawDescribe(
            $criteria->getObject(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $extra
        );
    }

    /**
     * @param string $object
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawDescribe(
        string $object,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        array $extra = []
    ) {
        return $this->rawDescribePipeline(
            $object,
            $connection,
            $cache,
            $transformer
        )($extra);
    }

    /**
     * @param ObjectAccessorCriteriaInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function describePipeline(
        ObjectAccessorCriteriaInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawDescribePipeline(
            $criteria->getObject(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }

    /**
     * @param string $object
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawDescribePipeline(
        string $object,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer, static::defaultTransformer()),
            [
                'resource' => [Describe::class]
            ]
        );

        return (new Resource(
            $this->rawHttpDescribeRelay(
                $object,
                ConnectionHelper::resolveConnection($connection),
                $cache
            ),
            $transformer,
            Force::getInstance()->getPsrLogger()
        ));
    }


    /**
     * @param ObjectAccessorCriteriaInterface $criteria
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpDescribeRelay(
        ObjectAccessorCriteriaInterface $criteria
    ): callable {
        return $this->rawHttpDescribeRelay(
            $criteria->getObject(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $object
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpDescribeRelay(
        string $object,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        $connection = ConnectionHelper::resolveConnection($connection);

        /** @var RelayBuilderInterface $builder */
        $builder = new Describe(
            $connection,
            $connection,
            CacheHelper::resolveCache($cache),
            $object,
            Force::getInstance()->getPsrLogger()
        );

        return $builder->build();
    }

    /**
     * @param ObjectAccessorCriteriaInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpDescribe(
        ObjectAccessorCriteriaInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpDescribe(
            $criteria->getObject(),
            $criteria->getConnection(),
            $criteria->getCache()
        )();
    }

    /**
     * @param string $object
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpDescribe(
        string $object,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpDescribeRelay(
            $object,
            $connection,
            $cache
        )();
    }
}
