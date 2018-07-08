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
class InstanceCriteria extends BaseObject implements InstanceCriteriaInterface
{
    use traits\ConnectionTrait,
        traits\CacheTrait,
        traits\TransformerCollectionTrait;

    /**
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function describe(array $config = [], $source = null)
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getGeneral()->describe($this, $source);
    }

    /**
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function limits(array $config = [], $source = null)
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getGeneral()->limits($this, $source);
    }

    /**
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function resources(array $config = [], $source = null)
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getGeneral()->resources($this, $source);
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
