<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\records;

use craft\helpers\Json;
use flipbox\ember\helpers\ModelHelper;
use flipbox\ember\helpers\ObjectHelper;
use flipbox\ember\records\ActiveRecordWithId;
use flipbox\ember\traits\HandleRules;
use flipbox\force\connections\ConnectionInterface;
use flipbox\force\criteria\QueryCriteria;
use flipbox\force\Force;
use flipbox\force\validators\ConnectionValidator;
use yii\validators\UniqueValidator;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property string $class
 * @property array $settings
 */
class Connection extends ActiveRecordWithId
{
    use HandleRules;

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
            $this->handleRules(),
            [
                [
                    [
                        'class'
                    ],
                    'required'
                ],
                [
                    [
                        'handle'
                    ],
                    UniqueValidator::class
                ],
                [
                    [
                        'class'
                    ],
                    ConnectionValidator::class
                ],
                [
                    [
                        'class',
                        'settings'
                    ],
                    'safe',
                    'on' => [
                        ModelHelper::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );
    }

    /**
     * @param static $record
     * @param $row
     */
    public static function populateRecord($record, $row)
    {
        parent::populateRecord($record, $row);

        $settings = $record->settings;

        if (is_string($settings)) {
            $settings = Json::decodeIfJson($settings);
        }

        $record->setOldAttribute('settings', $settings);
        $record->setAttribute('settings', $settings);
    }
}
