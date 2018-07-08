<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services\resources\traits;

use craft\base\Element;
use craft\base\ElementInterface;
use flipbox\force\connections\ConnectionInterface;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use flipbox\force\helpers\ConnectionHelper;
use flipbox\force\pipeline\Resource;
use flipbox\force\pipeline\stages\ElementAssociationStage;
use flipbox\force\pipeline\stages\ElementSaveStage;
use flipbox\force\traits\TransformElementIdTrait;
use flipbox\force\traits\TransformElementPayloadTrait;
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
     * @param string $id
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public abstract function rawHttpUpsertRelay(
        string $object,
        string $id,
        array $payload,
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

        (new Resource(
            $this->rawHttpReadRelay(
                $field->object,
                $id,
                ConnectionHelper::resolveConnection($connection),
                $cache
            ),
            null,
            Force::getInstance()->getPsrLogger()
        ))->build()->pipe(
            new ElementSaveStage($field)
        )->pipe(
            new ElementAssociationStage($field)
        )(null, $element);

        return !$element->hasErrors();
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

        (new Resource(
            $this->rawHttpUpsertRelay(
                $field->object,
                $id,
                $payload,
                $connection,
                $cache
            ),
            null,
            Force::getInstance()->getPsrLogger()
        ))->build()->pipe(
            new ElementAssociationStage($field)
        )(null, $element);

        return !$element->hasErrors();
    }
}
