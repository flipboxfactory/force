<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\queries\traits;

use flipbox\force\Force;
use flipbox\force\records\Query;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait Lookup
{
    /**
     * @inheritdoc
     * @return Query
     */
    protected function find($identifier)
    {
        return Force::getInstance()->getQueryManager()->find($identifier);
    }
}
