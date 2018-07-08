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
use flipbox\force\helpers\ConnectionHelper;
use flipbox\force\helpers\TransformerHelper;
use flipbox\force\pipeline\Resource;
use flipbox\force\transformers\collections\TransformerCollectionInterface;
use Flipbox\Relay\Builder\RelayBuilderInterface;
use Flipbox\Relay\Salesforce\Builder\Resources\SObject\Row\Create;
use League\Pipeline\PipelineBuilderInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait CreateObjectTrait
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
    public function create(
        ObjectMutatorCriteriaInterface $criteria,
        $source = null
    ) {
        return $this->rawCreate(
            $criteria->getObject(),
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getTransformer(),
            $source
        );
    }

    /**
     * @param string $object
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawCreate(
        string $object,
        array $payload,
        ConnectionInterface $connection = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawCreatePipeline(
            $object,
            $payload,
            $connection,
            $transformer
        )($source);
    }

    /**
     * @param ObjectMutatorCriteriaInterface $criteria
     * @param ConnectionInterface|string|null $connection
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function createPipeline(
        ObjectMutatorCriteriaInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawCreatePipeline(
            $criteria->getObject(),
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getTransformer()
        );
    }

    /**
     * @param string $object
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawCreatePipeline(
        string $object,
        array $payload,
        ConnectionInterface $connection = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer, static::defaultTransformer()),
            [
                'resource' => [Create::class]
            ]
        );

        return (new Resource(
            $this->rawHttpCreateRelay(
                $object,
                $payload,
                ConnectionHelper::resolveConnection($connection)
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
    public function httpCreateRelay(
        ObjectMutatorCriteriaInterface $criteria
    ): callable {
        return $this->rawHttpCreateRelay(
            $criteria->getObject(),
            $criteria->getPayload(),
            $criteria->getConnection()
        );
    }

    /**
     * @param string $object
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpCreateRelay(
        string $object,
        array $payload,
        ConnectionInterface $connection = null
    ): callable {
        $connection = ConnectionHelper::resolveConnection($connection);

        /** @var RelayBuilderInterface $builder */
        $builder = new Create(
            $connection,
            $connection,
            $object,
            $payload,
            Force::getInstance()->getPsrLogger()
        );

        return $builder->build();
    }

    /**
     * @param ObjectMutatorCriteriaInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpCreate(
        ObjectMutatorCriteriaInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpCreateRelay(
            $criteria->getObject(),
            $criteria->getPayload(),
            $criteria->getConnection()
        )();
    }

    /**
     * @param string $object
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpCreate(
        string $object,
        array $payload,
        ConnectionInterface $connection = null
    ): ResponseInterface {
        return $this->rawHttpCreateRelay(
            $object,
            $payload,
            $connection
        )();
    }
}
