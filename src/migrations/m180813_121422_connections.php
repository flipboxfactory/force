<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\migrations;

use flipbox\craft\integration\migrations\IntegrationConnections;
use flipbox\craft\salesforce\records\Connection as ConnectionRecord;

class m180813_121422_connections extends IntegrationConnections
{
    /**
     * @return string
     */
    protected static function tableName(): string
    {
        return ConnectionRecord::tableName();
    }
}
