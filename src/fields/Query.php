<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use flipbox\ember\helpers\ModelHelper;
use flipbox\force\criteria\QueryCriteria;
use flipbox\force\Force;
use flipbox\force\queries\traits\QueryBuilderAttributeTrait;
use flipbox\force\validators\QueryBuilderSettingsValidator;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Query extends Field
{
    use QueryBuilderAttributeTrait;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('force', 'Salesforce Query');
    }

    /**
     * @inheritdoc
     * @return QueryCriteria
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        return Force::getInstance()->getQueryField()->normalizeValue(
            $this,
            $value,
            $element
        );
    }

    /*******************************************
     * VIEWS
     *******************************************/

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Force::getInstance()->getQueryField()->getSettingsHtml($this);
    }

    /**
     * @param QueryCriteria $value
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        return Force::getInstance()->getQueryField()->getInputHtml($this, $value, $element);
    }

    /*******************************************
     * RULES
     *******************************************/

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
                        'query',
                    ],
                    QueryBuilderSettingsValidator::class,
                    'message' => Craft::t('force', 'Invalid Salesforce Query')
                ],
                [
                    [
                        'query'
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
     * @inheritdoc
     */
    public function getSettings(): array
    {
        $settings = parent::getSettings();
        $settings['query'] = $this->getQuery()->toConfig();

        return $settings;
    }
}
