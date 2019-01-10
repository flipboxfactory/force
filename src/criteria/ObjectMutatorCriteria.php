<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\criteria;

use Flipbox\Salesforce\Resources\SObject;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectMutatorCriteria extends AbstractCriteria implements ObjectMutatorCriteriaInterface
{
    use ConnectionTrait,
        CacheTrait,
        LoggerTrait;

    /**
     * @var string
     */
    public $object;

    /**
     * @var string
     */
    public $id;

    /**
     * @var array|null
     */
    public $payload;

    /**
     * @return string
     */
    public function getObject()
    {
        return (string)$this->object;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return empty($this->id) ? null : (string)$this->id;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return array_filter((array)$this->payload);
    }

    /**
     * @param array $criteria
     * @param array $config
     * @return ResponseInterface
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     */
    public function create(array $criteria = [], array $config = []): ResponseInterface
    {
        $this->populate($criteria);

        return SObject::create(
            $this->getConnection(),
            $this->getObject(),
            $this->getPayload(),
            $this->getLogger(),
            $config
        );
    }

    /**
     * @param array $criteria
     * @param array $config
     * @return ResponseInterface
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function update(array $criteria = [], array $config = []): ResponseInterface
    {
        $this->populate($criteria);

        return SObject::update(
            $this->getConnection(),
            $this->getCache(),
            $this->getObject(),
            $this->getPayload(),
            $this->getId(),
            $this->getLogger(),
            $config
        );
    }

    /**
     * @param array $criteria
     * @param array $config
     * @return ResponseInterface
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function upsert(array $criteria = [], array $config = []): ResponseInterface
    {
        $this->populate($criteria);

        return SObject::upsert(
            $this->getConnection(),
            $this->getCache(),
            $this->getObject(),
            $this->getPayload(),
            $this->getId(),
            $this->getLogger(),
            $config
        );
    }

    /**
     * @param array $criteria
     * @param array $config
     * @return ResponseInterface
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function delete(array $criteria = [], array $config = []): ResponseInterface
    {
        $this->populate($criteria);

        return SObject::delete(
            $this->getConnection(),
            $this->getCache(),
            $this->getObject(),
            $this->getId(),
            $this->getLogger(),
            $config
        );
    }
}
