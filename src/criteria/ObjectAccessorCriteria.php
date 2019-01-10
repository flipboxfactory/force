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
class ObjectAccessorCriteria extends AbstractCriteria implements ObjectAccessorCriteriaInterface
{
    use ConnectionTrait,
        CacheTrait,
        LoggerTrait;

    /**
     * @var string
     */
    public $object = '';

    /**
     * @var string
     */
    public $id = '';

    /**
     * @inheritdoc
     */
    public function getObject(): string
    {
        return (string)$this->object;
    }

    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return (string)$this->id;
    }

    /**
     * @param array $criteria
     * @param array $config
     * @return ResponseInterface
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function basic(array $criteria = [], array $config = []): ResponseInterface
    {
        $this->populate($criteria);

        return SObject::basic(
            $this->getConnection(),
            $this->getCache(),
            $this->getObject(),
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
    public function describe(array $criteria = [], array $config = []): ResponseInterface
    {
        $this->populate($criteria);

        return SObject::describe(
            $this->getConnection(),
            $this->getCache(),
            $this->getObject(),
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
    public function read(array $criteria = [], array $config = []): ResponseInterface
    {
        $this->populate($criteria);

        return SObject::read(
            $this->getConnection(),
            $this->getCache(),
            $this->getObject(),
            $this->getId(),
            $this->getLogger(),
            $config
        );
    }
}
