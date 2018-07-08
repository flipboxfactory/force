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
use Flipbox\Relay\Salesforce\Builder\Resources\SObject\Row\Delete;
use League\Pipeline\PipelineBuilderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait DeleteObjectTrait
{
    /**
     * @return array|TransformerCollectionInterface
     */
    public abstract static function defaultTransformer();

    /**
     * @param ObjectMutatorCriteriaInterface $criteria
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function delete(
        ObjectMutatorCriteriaInterface $criteria,
        $source = null
    ) {
        return $this->rawDelete(
            $criteria->getObject(),
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $source
        );
    }

    /**
     * @param string $object
     * @param string $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawDelete(
        string $object,
        string $id,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawDeletePipeline(
            $object,
            $id,
            $connection,
            $cache,
            $transformer
        )($source);
    }

    /**
     * @param ObjectMutatorCriteriaInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function deletePipeline(
        ObjectMutatorCriteriaInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawDeletePipeline(
            $criteria->getObject(),
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }

    /**
     * @param string $object
     * @param string $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawDeletePipeline(
        string $object,
        string $id,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer, static::defaultTransformer()),
            [
                'resource' => [Delete::class]
            ]
        );

        return (new Resource(
            $this->rawHttpDeleteRelay(
                $object,
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
    public function httpDeleteRelay(
        ObjectMutatorCriteriaInterface $criteria
    ): callable {
        return $this->rawHttpDeleteRelay(
            $criteria->getObject(),
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $object
     * @param string $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpDeleteRelay(
        string $object,
        string $id,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        $connection = ConnectionHelper::resolveConnection($connection);

        /** @var RelayBuilderInterface $builder */
        $builder = new Delete(
            $connection,
            $connection,
            CacheHelper::resolveCache($cache),
            $object,
            $id,
            Force::getInstance()->getPsrLogger()
        );

        return $builder->build();
    }

    /**
     * @param ObjectMutatorCriteriaInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpDelete(
        ObjectMutatorCriteriaInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpDelete(
            $criteria->getObject(),
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $object
     * @param string $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpDelete(
        string $object,
        string $id,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpDeleteRelay(
            $object,
            $id,
            $connection,
            $cache
        )();
    }
}
