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
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function create(array $config = [], array $extra = [])
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getObject()->create($this, $extra);
    }

    /**
     * @param array $config
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function update(array $config = [], array $extra = [])
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getObject()->update($this, $extra);
    }

    /**
     * @param array $config
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function upsert(array $config = [], array $extra = [])
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getObject()->upsert($this, $extra);
    }

    /**
     * @param array $config
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function delete(array $config = [], array $extra = [])
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getObject()->delete($this, $extra);
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
