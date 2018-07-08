<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\criteria\traits;

use flipbox\force\connections\ConnectionInterface;
use flipbox\force\helpers\ConnectionHelper;
use flipbox\force\services\Connections;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait ConnectionTrait
{
    /**
     * @var ConnectionInterface|string
     */
    protected $connection = Connections::DEFAULT_CONNECTION;

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
     * @throws \yii\base\InvalidConfigException
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->connection = ConnectionHelper::resolveConnection($this->connection);
    }
}
