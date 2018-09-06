<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\records;

use craft\helpers\Json;
use flipbox\ember\helpers\ModelHelper;
use flipbox\ember\records\ActiveRecordWithId;
use flipbox\ember\traits\HandleRules;
use flipbox\force\connections\ConnectionConfigurationInterface;
use flipbox\force\connections\DefaultConfiguration;
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
     * @var ConnectionConfigurationInterface
     */
    private $type;

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

    /**
     * @return ConnectionConfigurationInterface
     */
    public function getConfiguration(): ConnectionConfigurationInterface
    {
        if ($this->type === null) {

            if (null === ($type = Force::getInstance()->getConnectionManager()->findConfiguration($this))) {
                $type = new DefaultConfiguration($this);
            }

            $this->type = $type;
        }

        return $this->type;
    }
}
