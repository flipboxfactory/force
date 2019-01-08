<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\query;

use flipbox\craft\ember\actions\records\DeleteRecord;
use flipbox\force\records\SOQL;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class DeleteQuery extends DeleteRecord
{
    /**
     * @inheritdoc
     */
    public function run($query)
    {
        return parent::run($query);
    }

    /**
     * @inheritdoc
     * @return SOQL
     */
    protected function find($identifier)
    {
        return SOQL::findOne($identifier);
    }
}
