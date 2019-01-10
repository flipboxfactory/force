<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\criteria;

use flipbox\craft\salesforce\Force;
use flipbox\craft\salesforce\records\Connection;
use Flipbox\Salesforce\Connections\ConnectionInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait ConnectionTrait
{
    /**
     * @var ConnectionInterface|string
     */
    protected $connection;

    /**
     * @param $value
     * @return $this
     */
    public function connection($value)
    {
        return $this->setConnection($value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setConnection($value)
    {
        $this->connection = $value;
        return $this;
    }

    /**
     * @return ConnectionInterface
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->connection = $this->resolveConnection($this->connection);
    }

    /**
     * @param $connection
     * @return ConnectionInterface
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     */
    protected static function resolveConnection($connection): ConnectionInterface
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
