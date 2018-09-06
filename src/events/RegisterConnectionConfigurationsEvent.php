<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\events;

use yii\base\Event;

class RegisterConnectionConfigurationsEvent extends Event
{
    /**
     * @var array
     */
    public $configurations = [];
}
