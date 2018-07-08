<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\helpers;

use flipbox\force\connections\ConnectionInterface;
use flipbox\force\Force;
use flipbox\force\services\Connections;
use yii\base\InvalidConfigException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ConnectionHelper
{
    /**
     * @param null|string|ConnectionInterface $connection
     * @return ConnectionInterface
     * @throws InvalidConfigException
     */
    public static function resolveConnection($connection): ConnectionInterface
    {
        if ($connection instanceof ConnectionInterface) {
            return $connection;
        }

        if ($connection === null) {
            $connection = Connections::DEFAULT_CONNECTION;
        }

        return Force::getInstance()->getConnections()->get($connection);
    }
}
