<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services\resources\traits;

use flipbox\force\connections\ConnectionInterface;
use flipbox\force\criteria\ObjectMutatorCriteriaInterface;
use flipbox\force\Force;
use flipbox\force\helpers\CacheHelper;
use flipbox\force\helpers\ConnectionHelper;
use flipbox\force\helpers\TransformerHelper;
use flipbox\force\pipeline\Resource;
use flipbox\force\transformers\collections\TransformerCollectionInterface;
use Flipbox\Relay\Builder\RelayBuilderInterface;
use Flipbox\Relay\Salesforce\Builder\Resources\SObject\Row\Upsert;
use League\Pipeline\PipelineBuilderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait UpsertObjectTrait
{
    /**
     * @return array|TransformerCollectionInterface
     */
    public abstract static function defaultTransformer();

    /**
     * @param ObjectMutatorCriteriaInterface $criteria
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function upsert(
        ObjectMutatorCriteriaInterface $criteria,
        array $extra = []
    ) {
        return $this->rawUpsert(
            $criteria->getObject(),
            $criteria->getPayload(),
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $extra
        );
    }

    /**
     * @param string $object
     * @param array $payload
     * @param string|null $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawUpsert(
        string $object,
        array $payload,
        string $id = null,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        array $extra = []
    ) {
        return $this->rawUpsertPipeline(
            $object,
            $payload,
            $id,
            $connection,
            $cache,
            $transformer
        )($extra);
    }

    /**
     * @param ObjectMutatorCriteriaInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function upsertPipeline(
        ObjectMutatorCriteriaInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawUpsertPipeline(
            $criteria->getObject(),
            $criteria->getPayload(),
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }

    /**
     * @param string $object
     * @param string $id
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawUpsertPipeline(
        string $object,
        array $payload,
        string $id = null,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer, static::defaultTransformer()),
            [
                'resource' => [Upsert::class]
            ]
        );

        return (new Resource(
            $this->rawHttpUpsertRelay(
                $object,
                $payload,
                $id,
                ConnectionHelper::resolveConnection($connection),
                $cache
            ),
            $transformer,
            Force::getInstance()->getPsrLogger()
        ));
    }

    /**
     * @param ObjectMutatorCriteriaInterface $criteria
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpUpsertRelay(
        ObjectMutatorCriteriaInterface $criteria
    ): callable {
        return $this->rawHttpUpsertRelay(
            $criteria->getObject(),
            $criteria->getPayload(),
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $object
     * @param array $payload
     * @param string|null $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpUpsertRelay(
        string $object,
        array $payload,
        string $id = null,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        $connection = ConnectionHelper::resolveConnection($connection);

        /** @var RelayBuilderInterface $builder */
        $builder = new Upsert(
            $connection,
            $connection,
            CacheHelper::resolveCache($cache),
            $object,
            $payload,
            empty($id) ? null : $id,
            Force::getInstance()->getPsrLogger()
        );

        return $builder->build();
    }

    /**
     * @param ObjectMutatorCriteriaInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpUpsert(
        ObjectMutatorCriteriaInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpUpsert(
            $criteria->getObject(),
            $criteria->getPayload(),
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $object
     * @param string $id
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpUpsert(
        string $object,
        array $payload,
        string $id = null,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpUpsertRelay(
            $object,
            $payload,
            $id,
            $connection,
            $cache
        )();
    }
}
