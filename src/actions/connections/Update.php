<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\connections;

use flipbox\craft\integration\actions\connections\Update as BaseUpdate;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Update extends BaseUpdate
{
    use traits\Lookup;
}
