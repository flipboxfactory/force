<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\actions\connections;

use flipbox\craft\ember\actions\records\DeleteRecord;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class DeleteConnection extends DeleteRecord
{
    use LookupConnectionTrait;

    /**
     * @inheritdoc
     */
    public function run($connection)
    {
        return parent::run($connection);
    }
}
