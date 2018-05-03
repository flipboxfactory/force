<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services\resources;

use flipbox\ember\helpers\ObjectHelper;
use flipbox\force\criteria\SObjectCriteria;
use flipbox\force\Force;
use flipbox\force\helpers\TransformerHelper;
use Flipbox\Salesforce\Connections\ConnectionInterface;
use Flipbox\Salesforce\Resources\SObject\Describe as SObjectDescribe;
use Flipbox\Salesforce\Resources\SObject\Row\Get as SObjectRowGet;
use Flipbox\Salesforce\Resources\SObject\Row\Upsert as SObjectRowUpsert;
use Flipbox\Salesforce\Transformers\Collections\TransformerCollectionInterface;
use Flipbox\Transform\Factory;
use Psr\SimpleCache\CacheInterface;
use yii\base\Component;
use yii\helpers\Json;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SObject extends Component
{
    /**
     * The transformer key used to retrieve a SObject Id transformer from the transformer collection.
     */
    const ID_TRANSFORMER_KEY = 'id';

    /**
     * The transformer key used to retrieve a SObject Payload transformer from the transformer collection.
     */
    const PAYLOAD_TRANSFORMER_KEY = 'payload';


    /*******************************************
     * CRITERIA
     *******************************************/

    /**
     * @param array $criteria
     * @return SObjectCriteria
     */
    public function getCriteria(array $criteria = []): SObjectCriteria
    {
        $object = new SObjectCriteria();

        ObjectHelper::populate(
            $object,
            $criteria
        );

        return $object;
    }


    /*******************************************
     * DESCRIBE
     *******************************************/

    /**
     * @param string $sObject
     * @param ConnectionInterface $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|null $transformer
     * @return SObjectDescribe
     */
    public function describe(
        ConnectionInterface $connection,
        CacheInterface $cache,
        string $sObject,
        TransformerCollectionInterface $transformer = null
    ) {
        return (new SObjectDescribe(
            $sObject,
            $connection,
            $cache,
            TransformerHelper::populateTransformerCollection($transformer, [
                'resource' => [SObjectDescribe::class],
                'handle' => ['sobject', $sObject]
            ])
        ));
    }

    /**
     * @param SObjectCriteria $criteria
     * @return mixed
     */
    public function describeFromCriteria(
        SObjectCriteria $criteria
    ) {
        return $this->describe(
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->sObject,
            $criteria->getTransformer()
        );
    }


    /*******************************************
     * GET ROW
     *******************************************/

    /**
     * @param string $sObject
     * @param string $id
     * @param ConnectionInterface $connection
     * @param CacheInterface $cache
     * @param TransformerCollectionInterface|null $transformer
     * @return mixed
     */
    public function getRow(
        ConnectionInterface $connection,
        CacheInterface $cache,
        string $sObject,
        $id,
        TransformerCollectionInterface $transformer = null
    ): SObjectRowGet {
        $transformer = TransformerHelper::populateTransformerCollection($transformer, [
            'resource' => [SObjectRowGet::class],
            'handle' => ['sobject', $sObject]
        ]);

        return (new SObjectRowGet(
            $sObject,
            $this->transformSObjectId($id, $transformer),
            $connection,
            $cache,
            $transformer
        ));
    }

    /**
     * @param SObjectCriteria $criteria
     * @return SObjectRowGet
     */
    public function getRowFromCriteria(
        SObjectCriteria $criteria
    ): SObjectRowGet {

        return $this->getRow(
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->sObject,
            $criteria->id,
            $criteria->getTransformer()
        );
    }

    /*******************************************
     * UPSERT ROW
     *******************************************/

    /**
     * @param string $sObject
     * @param mixed $payload
     * @param string $id
     * @param ConnectionInterface $connection
     * @param CacheInterface $cache
     * @param TransformerCollectionInterface|null $transformer
     * @return mixed
     */
    public function upsertRow(
        ConnectionInterface $connection,
        CacheInterface $cache,
        string $sObject,
        $payload,
        $id = null,
        TransformerCollectionInterface $transformer = null
    ): SObjectRowUpsert {
        $transformer = TransformerHelper::populateTransformerCollection($transformer, [
            'resource' => [SObjectRowUpsert::class],
            'handle' => ['sobject', $sObject]
        ]);

        return (new SObjectRowUpsert(
            $sObject,
            $this->transformSObjectPayload($payload, $transformer, $sObject),
            $connection,
            $cache,
            $this->transformSObjectId($id, $transformer, $sObject),
            $transformer
        ));
    }


    /*******************************************
     * TRANSFORM
     *******************************************/


    /**
     * @param $id
     * @param TransformerCollectionInterface|null $transformer
     * @param string|null $sObject
     * @param string $default
     * @return null|string
     */
    protected function transformSObjectId(
        $id,
        TransformerCollectionInterface $transformer = null,
        string $sObject = null,
        $default = '__UNKNOWN_ID__'
    ) {
        if (is_string($id) && !empty($id)) {
            Force::debug(sprintf(
                "The SObject Id is already a string; no transformation needed. '%s' is being returned.",
                (string)$id
            ), __METHOD__);
            return (string)$id;
        }

        if (empty($id)) {
            Force::debug(
                "The SObject Id is empty; assuming this is intentional for insert actions. 'nill' is being returned.",
                __METHOD__
            );
            return null;
        }

        if (null === ($transformer = $this->resolveSObjectIdTransformer($transformer))) {
            Force::debug(sprintf(
                "Unable to resolve transformer. '%s' (the default value) is being returned.",
                (string)$default
            ), __METHOD__);
            return $default;
        };

        return (string)Factory::item(
            $transformer,
            $id,
            [],
            [
                'sObject' => $sObject
            ]
        );
    }

    /**
     * @param $payload
     * @param TransformerCollectionInterface|null $transformer
     * @param string|null $sObject
     * @return array
     */
    protected function transformSObjectPayload(
        $payload,
        TransformerCollectionInterface $transformer = null,
        string $sObject = null
    ): array {

        if (is_array($payload)) {
            Force::debug(sprintf(
                "The SObject payload is already an array; no transformation needed. '%s' is being returned.",
                (string)Json::encode($payload)
            ), __METHOD__);
            return $payload;
        }

        if (null === ($transformer = $this->resolveSObjectPayloadTransformer($transformer))) {
            Force::debug("Unable to resolve transformer. An empty array is being returned.", __METHOD__);
            return [];
        };

        return (array)Factory::item(
            $transformer,
            $payload,
            [],
            [
                'sObject' => $sObject
            ]
        );
    }


    /*******************************************
     * RESOLVE
     *******************************************/

    /**
     * @param TransformerCollectionInterface|null $transformer
     * @return callable|\Flipbox\Transform\Transformers\TransformerInterface|null
     */
    private function resolveSObjectIdTransformer(TransformerCollectionInterface $transformer = null)
    {
        if ($transformer === null) {
            return null;
        }

        try {
            return TransformerHelper::resolve(
                $transformer->getTransformer(self::ID_TRANSFORMER_KEY)
            );
        } catch (\Throwable $e) {
            Force::error(sprintf(
                "An exception occurred while trying to resolve a transformer: '%s'",
                (string)$e->getMessage()
            ), __METHOD__);
        }

        return null;
    }

    /**
     * @param TransformerCollectionInterface|null $transformer
     * @return callable|\Flipbox\Transform\Transformers\TransformerInterface|null
     */
    private function resolveSObjectPayloadTransformer(TransformerCollectionInterface $transformer = null)
    {
        if ($transformer === null) {
            return null;
        }

        try {
            return TransformerHelper::resolve(
                $transformer->getTransformer(self::PAYLOAD_TRANSFORMER_KEY)
            );
        } catch (\Throwable $e) {
            Force::error(sprintf(
                "An exception occurred while trying to resolve a transformer: '%s'",
                (string)$e->getMessage()
            ), __METHOD__);
        }

        return null;
    }
}
