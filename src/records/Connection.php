<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\records;

use flipbox\craft\integration\records\IntegrationConnection;
use flipbox\craft\integration\services\IntegrationConnectionManager;
use flipbox\force\Force;
use flipbox\force\validators\ConnectionValidator;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Connection extends IntegrationConnection
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
                ],
                [
                    [
                        'settings'
                    ],
                    'required',
                    'message' => 'A value is required.'
                ],
                [
                    [
                        'settings'
                    ],
                    'each',
                    'rule' => ['required', 'message' => 'A value is required.']
                ]
            ]
        );
    }

    /**
     * @return IntegrationConnectionManager
     */
    protected function getConnectionManager(): IntegrationConnectionManager
    {
        return Force::getInstance()->getConnectionManager();
    }
}
