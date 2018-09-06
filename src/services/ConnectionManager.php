<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services;

use flipbox\craft\integration\services\IntegrationConnectionManager;
use flipbox\force\records\Connection;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method Connection  create(array $attributes = [])
 * @method Connection  find($identifier)
 * @method Connection  get($identifier)
 * @method Connection  findByString($identifier)
 * @method Connection  getByString($identifier)
 * @method Connection  findByCondition($condition = [])
 * @method Connection  getByCondition($condition = [])
 * @method Connection  findByCriteria($criteria = [])
 * @method Connection  getByCriteria($criteria = [])
 * @method Connection [] findAllByCondition($condition = [])
 * @method Connection [] getAllByCondition($condition = [])
 * @method Connection [] findAllByCriteria($criteria = [])
 * @method Connection [] getAllByCriteria($criteria = [])
 */
class ConnectionManager extends IntegrationConnectionManager
{
    /**
     * @inheritdoc
     */
    public static function recordClass(): string
    {
        return Connection ::class;
    }
}
