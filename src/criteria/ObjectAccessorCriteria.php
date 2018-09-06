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
class ObjectAccessorCriteria extends BaseObject implements ObjectAccessorCriteriaInterface
{
    use traits\ConnectionTrait,
        traits\CacheTrait,
        traits\TransformerCollectionTrait;

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
     * @param array $config
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function read(array $config = [], array $extra = [])
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getObject()->read($this, $extra);
    }

    /**
     * @param array $config
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function describe(array $config = [], array $extra = [])
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getObject()->describe($this, $extra);
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
