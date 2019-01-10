<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\actions\connections;

use flipbox\craft\salesforce\records\Connection;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait LookupConnectionTrait
{
    /**
     * @inheritdoc
     * @return Connection
     */
    protected function find($identifier)
    {
        return Connection::findOne($identifier);
    }
}
