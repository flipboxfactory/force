<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\helpers;

use flipbox\force\Force;
use flipbox\force\records\Connection;
use Flipbox\Salesforce\Connections\ConnectionInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ConnectionHelper
{
    /**
     * @param $connection
     * @return ConnectionInterface
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     */
    public static function resolveConnection($connection): ConnectionInterface
    {
        if ($connection instanceof ConnectionInterface) {
            return $connection;
        }

        if ($connection === null) {
            $connection = Force::getInstance()->getSettings()->getDefaultConnection();
        }

        return Connection::getOne($connection);
    }
}
