<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\events;

use craft\base\ElementInterface;
use flipbox\force\fields\actions\SObjectActionInterface;
use flipbox\force\fields\actions\SObjectRowActionInterface;
use yii\base\Event;

class RegisterSObjectFieldActionsEvent extends Event
{
    /**
     * @var array|SObjectActionInterface[]|SObjectRowActionInterface[]
     */
    public $actions = [];

    /**
     * @var ElementInterface
     */
    public $element;
}
