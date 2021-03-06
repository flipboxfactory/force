<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\events;

use flipbox\force\connections\ConnectionInterface;
use yii\base\Event;

class RegisterConnectionsEvent extends Event
{
    /**
     * @var array|ConnectionInterface[]
     */
    public $connections = [];
}
