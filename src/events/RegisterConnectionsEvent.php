<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\events;

use Flipbox\Salesforce\Connections\ConnectionInterface;
use yii\base\Event;

class RegisterConnectionsEvent extends Event
{
    /**
     * Event to register connections
     */
    const REGISTER_CONNECTIONS = 'registerConnections';

    /**
     * @var array|ConnectionInterface[]
     */
    public $connections = [];
}
