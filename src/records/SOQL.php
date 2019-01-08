<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\records;

use Craft;
use craft\helpers\Json;
use flipbox\craft\ember\helpers\ModelHelper;
use flipbox\craft\ember\models\HandleRulesTrait;
use flipbox\craft\ember\records\ActiveRecordWithId;
use flipbox\force\validators\QueryBuilderSettingsValidator;
use Flipbox\Salesforce\Criteria\QueryCriteria;
use yii\validators\UniqueValidator;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property string $name
 * @property string $settings
 */
class SOQL extends ActiveRecordWithId
{
    use HandleRulesTrait;

    /**
     * The table name
     */
    const TABLE_ALIAS = 'salesforce_queries';

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
                        'name'
                    ],
                    'required'
                ],
                [
                    [
                        'name',
                    ],
                    'string',
                    'max' => 255
                ],
                [
                    [
                        'settings',
                    ],
                    QueryBuilderSettingsValidator::class,
                    'attribute' => 'query',
                    'message' => Craft::t('force', 'Invalid Salesforce query')
                ],
                [
                    [
                        'handle'
                    ],
                    UniqueValidator::class
                ],
                [
                    [
                        'name',
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
     * @return QueryCriteria
     *
     * @deprecated
     */
    public function getCriteria(): QueryCriteria
    {
        return new QueryCriteria($this->getAttributes());
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
