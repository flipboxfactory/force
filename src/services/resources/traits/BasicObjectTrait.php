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
use Flipbox\Relay\Salesforce\Builder\Resources\SObject\Basic;
use League\Pipeline\PipelineBuilderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait BasicObjectTrait
{
    /**
     * @return array|TransformerCollectionInterface
     */
    public abstract static function defaultTransformer();

    /**
     * @param ObjectAccessorCriteriaInterface $criteria
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function basic(
        ObjectAccessorCriteriaInterface $criteria,
        $source = null
    ) {
        return $this->rawBasic(
            $criteria->getObject(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $source
        );
    }

    /**
     * @param string $sObject
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawBasic(
        string $sObject,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawBasicPipeline(
            $sObject,
            $connection,
            $cache,
            $transformer
        )($source);
    }

    /**
     * @param ObjectAccessorCriteriaInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function basicPipeline(
        ObjectAccessorCriteriaInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawBasicPipeline(
            $criteria->getObject(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }

    /**
     * @param string $sObject
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawBasicPipeline(
        string $sObject,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer, static::defaultTransformer()),
            [
                'resource' => [Basic::class]
            ]
        );

        return (new Resource(
            $this->rawHttpBasicRelay(
                $sObject,
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
    public function httpBasicRelay(
        ObjectAccessorCriteriaInterface $criteria
    ): callable {
        return $this->rawHttpBasicRelay(
            $criteria->getObject(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $sObject
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpBasicRelay(
        string $sObject,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        $connection = ConnectionHelper::resolveConnection($connection);

        /** @var RelayBuilderInterface $builder */
        $builder = new Basic(
            $connection,
            $connection,
            CacheHelper::resolveCache($cache),
            $sObject,
            Force::getInstance()->getPsrLogger()
        );

        return $builder->build();
    }

    /**
     * @param ObjectAccessorCriteriaInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpBasic(
        ObjectAccessorCriteriaInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpBasic(
            $criteria->getObject(),
            $criteria->getConnection(),
            $criteria->getCache()
        )();
    }

    /**
     * @param string $sObject
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpBasic(
        string $sObject,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpBasicRelay(
            $sObject,
            $connection,
            $cache
        )();
    }
}
