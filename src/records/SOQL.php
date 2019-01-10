<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\records;

use Craft;
use craft\helpers\Json;
use flipbox\craft\ember\helpers\ModelHelper;
use flipbox\craft\ember\models\HandleRulesTrait;
use flipbox\craft\ember\records\ActiveRecordWithId;
use flipbox\craft\salesforce\queries\SOQLQuery;
use flipbox\craft\salesforce\validators\QueryBuilderValidator;
use flipbox\craft\salesforce\criteria\QueryCriteria;
use Flipbox\Salesforce\Query\QueryBuilderInterface;
use yii\validators\UniqueValidator;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property string $name
 * @property string $settings
 * @property string $class
 * @property string $soql
 */
class SOQL extends ActiveRecordWithId implements QueryBuilderInterface
{
    use HandleRulesTrait;

    /**
     * The table name
     */
    const TABLE_ALIAS = 'salesforce_queries';

    /**
     * The Active Query class
     */
    const ACTIVE_QUERY_CLASS = SOQLQuery::class;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Always this class
        $this->class = static::class;
    }


    /*******************************************
     * SETTINGS
     *******************************************/

    /**
     * @inheritdoc
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function settingsHtml(): string
    {
        return Craft::$app->getView()->renderTemplate(
            'salesforce/_components/queries/dynamic',
            [
                'record' => $this
            ]
        );
    }

    /*******************************************
     * QUERY
     *******************************************/

    /**
     * @inheritdoc
     */
    public static function find()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Craft::createObject(
            static::ACTIVE_QUERY_CLASS,
            [
                get_called_class(),
                [
                    'class' => static::class
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected static function findByCondition($condition)
    {
        if (!is_numeric($condition) && is_string($condition)) {
            $condition = ['handle' => $condition];
        }

        /** @noinspection PhpInternalEntityUsedInspection */
        return parent::findByCondition($condition);
    }

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
                    QueryBuilderValidator::class
                ],
                [
                    [
                        'name'
                    ],
                    'required'
                ],
                [
                    [
                        'class'
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
                        'handle'
                    ],
                    UniqueValidator::class
                ],
                [
                    [
                        'name',
                        'settings',
                        'soql'
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
        return new QueryCriteria([
            'query' => $this
        ]);
    }

    /*******************************************
     * EVENTS
     *******************************************/

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->ensureSettings();
    }


    /*******************************************
     * NEW RECORD
     *******************************************/

    /**
     * @inheritdoc
     */
    public static function instantiate($row)
    {
        $class = $row['class'] ?? static::class;
        return new $class;
    }

    /**
     * @param static $record
     * @param $row
     */
    public static function populateRecord($record, $row)
    {
        parent::populateRecord($record, $row);

        // Ensure settings is an array
        $record->setOldAttribute('settings', $record->ensureSettings());
    }


    /*******************************************
     * SETTINGS
     *******************************************/

    /**
     * @param string $attribute
     * @return mixed
     */
    public function getSettingsValue(string $attribute)
    {
        $settings = $this->ensureSettings();
        return $settings[$attribute] ?? null;
    }

    /**
     * @param string $attribute
     * @param $value
     * @return $this
     */
    public function setSettingsValue(string $attribute, $value)
    {
        $settings = $this->ensureSettings();
        $settings[$attribute] = $value;

        $this->setAttribute('settings', $settings);
        return $this;
    }

    /**
     * @return array|null
     */
    protected function ensureSettings()
    {
        $settings = $this->getAttribute('settings');

        if (is_string($settings)) {
            $settings = Json::decodeIfJson($settings);
        }

        $this->setAttribute('settings', $settings);

        return $settings;
    }


    /*******************************************
     * BUILDER INTERFACE
     *******************************************/

    /**
     * @return string
     */
    public function build(): string
    {
        if (null === ($soql = $this->soql)) {
            return '';
        }

        return Craft::$app->getView()->renderString(
            $soql,
            (array)($this->getSettingsValue('variables') ?: [])
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }
}
