<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\records;

use flipbox\craft\integration\records\IntegrationConnection;
use flipbox\craft\salesforce\validators\ConnectionValidator;
use Flipbox\Salesforce\Connections\ConnectionInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class Connection extends IntegrationConnection implements ConnectionInterface
{
    /**
     * The table name
     */
    const TABLE_ALIAS = 'salesforce_connections';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    [
                        'class'
                    ],
                    ConnectionValidator::class
                ]
            ]
        );
    }
}
