<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\services;

use flipbox\ember\services\traits\records\AccessorByString;
use flipbox\force\records\Query as QueryRecord;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method QueryRecord create(array $attributes = [])
 * @method QueryRecord find($identifier)
 * @method QueryRecord get($identifier)
 * @method QueryRecord findByString($identifier)
 * @method QueryRecord getByString($identifier)
 * @method QueryRecord findByCondition($condition = [])
 * @method QueryRecord getByCondition($condition = [])
 * @method QueryRecord findByCriteria($criteria = [])
 * @method QueryRecord getByCriteria($criteria = [])
 * @method QueryRecord[] findAllByCondition($condition = [])
 * @method QueryRecord[] getAllByCondition($condition = [])
 * @method QueryRecord[] findAllByCriteria($criteria = [])
 * @method QueryRecord[] getAllByCriteria($criteria = [])
 */
class QueryManager extends Component
{
    use AccessorByString;

    /**
     * @inheritdoc
     */
    public static function recordClass(): string
    {
        return QueryRecord::class;
    }

    /**
     * @inheritdoc
     */
    protected function stringProperty(): string
    {
        return 'handle';
    }
}
