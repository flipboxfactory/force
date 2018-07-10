<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\events;

use craft\base\ElementInterface;
use flipbox\force\fields\actions\ObjectActionInterface;
use flipbox\force\fields\actions\ObjectItemActionInterface;
use yii\base\Event;

class RegisterObjectFieldActionsEvent extends Event
{
    /**
     * @var array|ObjectActionInterface[]|ObjectItemActionInterface[]
     */
    public $actions = [];

    /**
     * @var ElementInterface
     */
    public $element;
}
