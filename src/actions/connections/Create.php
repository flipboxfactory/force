<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\connections;

use flipbox\force\Force;
use yii\db\ActiveRecord;
use flipbox\craft\integration\actions\connections\Create as BaseCreate;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Create extends BaseCreate
{
    /**
     * @inheritdoc
     */
    protected function newRecord(array $config = []): ActiveRecord
    {
        return Force::getInstance()->getConnectionManager()->create($config);
    }
}
