<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\actions\connections;

use flipbox\force\Force;
use flipbox\craft\integration\actions\connections\Update as BaseUpdate;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Update extends BaseUpdate
{
    /**
     * @inheritdoc
     */
    protected function find($identifier)
    {
        return Force::getInstance()->getConnectionManager()->find($identifier);
    }
}
