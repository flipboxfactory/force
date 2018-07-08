<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\criteria;

use flipbox\ember\helpers\ObjectHelper;
use flipbox\force\Force;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectMutatorCriteria extends BaseObject implements ObjectMutatorCriteriaInterface
{
    use traits\ConnectionTrait,
        traits\CacheTrait,
        traits\TransformerCollectionTrait;

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
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function create(array $config = [], $source = null)
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getSObject()->create($this, $source);
    }

    /**
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function update(array $config = [], $source = null)
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getSObject()->update($this, $source);
    }

    /**
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function upsert(array $config = [], $source = null)
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getSObject()->upsert($this, $source);
    }

    /**
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function delete(array $config = [], $source = null)
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getSObject()->delete($this, $source);
    }

    /**
     * @inheritdoc
     */
    protected function prepare(array $criteria = [])
    {
        ObjectHelper::populate(
            $this,
            $criteria
        );
    }
}
