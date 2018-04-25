<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\criteria;

use flipbox\force\Force;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SObjectCriteria extends ResourceCriteria
{
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
    public function get(array $config = [], $source = null)
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getSObject()->getRowFromCriteria($this)->execute($source);
    }

    /**
     * @inheritdoc
     */
    public function describe(array $config = [], $source = null)
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getSObject()->describeFromCriteria($this)->execute($source);
    }
}
