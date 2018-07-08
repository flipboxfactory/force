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
 *
 * @deprecated
 */
class SObjectCriteria extends BaseObject implements SObjectCriteriaInterface
{
    use traits\ConnectionTrait,
        traits\CacheTrait,
        traits\TransformerCollectionTrait;

    /**
     * @var string|null
     */
    public $sObject = '';

    /**
     * @var mixed
     */
    public $id = '';

    /**
     * @var mixed
     */
    public $payload = '';

    /**
     * @inheritdoc
     */
    public function getSObject(): string
    {
        return (string)$this->sObject;
    }

    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return (string)$this->id;
    }

    /**
     * @inheritdoc
     */
    public function get(array $config = [], $source = null)
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getSObject()->getRowFromCriteria($this)->execute($source);
    }

    /**
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function describe(array $config = [], $source = null)
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getSObject()->describe($this, $source);
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
