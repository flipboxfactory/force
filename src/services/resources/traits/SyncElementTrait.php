<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services\resources\traits;

use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\Json;
use flipbox\force\connections\ConnectionInterface;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use flipbox\force\helpers\ConnectionHelper;
use flipbox\force\pipeline\stages\ElementAssociationStage;
use flipbox\force\pipeline\stages\ElementPopulateStage;
use flipbox\force\pipeline\stages\ElementSaveStage;
use flipbox\force\traits\TransformElementIdTrait;
use flipbox\force\traits\TransformElementPayloadTrait;
use flipbox\force\transformers\error\Interpret;
use Flipbox\Pipeline\Pipelines\Pipeline;
use Flipbox\Transform\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait SyncElementTrait
{
    use TransformElementIdTrait,
        TransformElementPayloadTrait;

    /**
     * @param string $object
     * @param string $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     */
    public abstract function rawHttpReadRelay(
        string $object,
        string $id,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable;

    /**
     * @param string $object
     * @param array $payload
     * @param string|null $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public abstract function rawHttpUpsertRelay(
        string $object,
        array $payload,
        string $id = null,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable;


    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param ConnectionInterface|null $connection
     * @param CacheInterface|null $cache
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function syncDown(
        ElementInterface $element,
        Objects $field,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): bool {
        /** @var Element $element */

        /** @var string $id */
        if (null === ($id = $this->transformElementId($element, $field))) {
            return false;
        }

        return $this->rawSyncDown(
            $element,
            $field,
            $id,
            $connection,
            $cache
        );
    }

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param string $id
     * @param ConnectionInterface|null $connection
     * @param CacheInterface|null $cache
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function rawSyncDown(
        ElementInterface $element,
        Objects $field,
        string $id,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): bool {
        /** @var Element $element */

        /** @var ResponseInterface $response */
        $response = $this->rawHttpReadRelay(
            $field->object,
            $id,
            ConnectionHelper::resolveConnection($connection),
            $cache
        )();

        return $this->handleSyncDownResponse(
            $response,
            $element,
            $field
        );
    }

    /**
     * @param ResponseInterface $response
     * @param ElementInterface $element
     * @param Objects $field
     * @return bool
     */
    protected function handleSyncDownResponse(
        ResponseInterface $response,
        ElementInterface $element,
        Objects $field
    ): bool {
        $logger = Force::getInstance()->getPsrLogger();

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299) {
            $pipeline = new Pipeline([
                'stages' => [
                    new ElementPopulateStage($field, ['logger' => $logger]),
                    new ElementSaveStage($field, ['logger' => $logger])
                ]
            ]);

            return $pipeline->process($response, ['element' => $element]) instanceof ResponseInterface;
        }

        $this->handleResponseErrors($response, $element);

        return false;
    }

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param ConnectionInterface|null $connection
     * @param CacheInterface|null $cache
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */

    public function syncUp(
        ElementInterface $element,
        Objects $field,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): bool {
        return $this->rawSyncUp(
            $element,
            $field,
            $this->transformElementPayload($element, $field),
            $this->transformElementId($element, $field),
            $connection,
            $cache
        );
    }

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param array $payload
     * @param string|null $id
     * @param ConnectionInterface|null $connection
     * @param CacheInterface|null $cache
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function rawSyncUp(
        ElementInterface $element,
        Objects $field,
        array $payload,
        string $id = null,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): bool {
        /** @var Element $element */

        /** @var ResponseInterface $response */
        $response = $this->rawHttpUpsertRelay(
            $field->object,
            $payload,
            $id,
            $connection,
            $cache
        )();

        return $this->handleSyncUpResponse(
            $response,
            $element,
            $field,
            $id
        );
    }

    /**
     * @param ResponseInterface $response
     * @param ElementInterface $element
     * @param Objects $field
     * @param string|null $id
     * @return bool
     */
    protected function handleSyncUpResponse(
        ResponseInterface $response,
        ElementInterface $element,
        Objects $field,
        string $id = null
    ): bool {
        $logger = Force::getInstance()->getPsrLogger();

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299) {
            if (empty($id)) {
                $pipeline = new Pipeline([
                    'stages' => [
                        new ElementAssociationStage($field, ['logger' => $logger])
                    ]
                ]);

                return $pipeline->process($response, ['element' => $element]) instanceof ResponseInterface;
            }

            return true;
        }

        $this->handleResponseErrors($response, $element);

        return false;
    }

    /**
     * @param ResponseInterface $response
     * @param ElementInterface $element
     */
    protected function handleResponseErrors(ResponseInterface $response, ElementInterface $element)
    {
        /** @var Element $element */

        $data = Json::decodeIfJson(
            $response->getBody()->getContents()
        );

        $errors = (array)Factory::item(
            new Interpret(),
            $data
        );

        $errors = array_filter($errors);

        if (empty($errors)) {
            $element->addErrors($errors);
        }
    }
}