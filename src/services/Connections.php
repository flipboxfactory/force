<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services;

use flipbox\force\connections\ConnectionInterface;
use flipbox\force\events\RegisterConnectionsEvent;
use flipbox\force\Force;
use yii\base\InvalidConfigException;
use yii\di\ServiceLocator;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Connections extends ServiceLocator
{
    /**
     * @event RegisterConnectionsEvent The event that is triggered when registering user permissions.
     */
    const EVENT_REGISTER_CONNECTIONS = 'registerConnections';

    /**
     * The default connection handle
     */
    const APP_CONNECTION = 'app';

    /**
     * The default connection identifier
     */
    const DEFAULT_CONNECTION = 'DEFAULT';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $event = new RegisterConnectionsEvent([
            'connections' => []
        ]);

        $this->trigger(self::EVENT_REGISTER_CONNECTIONS, $event);

        $this->setComponents(
            $event->connections
        );
    }

    /**
     * @inheritdoc
     * @return ConnectionInterface
     */
    public function get($id, $throwException = true)
    {
        if ($id === self::DEFAULT_CONNECTION) {
            $id = Force::getInstance()->getSettings()->getDefaultConnection();
        }

        $connection = parent::get($id, $throwException);

        if (!$connection instanceof ConnectionInterface) {
            throw new InvalidConfigException(sprintf(
                "Connection '%s' must be an instance of '%s', '%s' given.",
                (string)$id,
                ConnectionInterface::class,
                get_class($connection)
            ));
        }
        return $connection;
    }

    /**
     * @param bool $throwException
     * @return ConnectionInterface[]
     * @throws InvalidConfigException
     */
    public function getAll($throwException = true)
    {
        $components = [];

        foreach ($this->getComponents(true) as $id => $component) {
            $components[$id] = $this->get($id, $throwException);
        }

        return $components;
    }
}
