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
use flipbox\craft\ember\helpers\ModelHelper;
use flipbox\force\query\settings\DynamicQuerySettings;
use flipbox\force\validators\QueryBuilderSettingsValidator;
use Flipbox\Salesforce\Criteria\QueryCriteria;
use Flipbox\Salesforce\Query\DynamicQueryBuilder;
use Flipbox\Salesforce\Query\QueryBuilderAttributeTrait;
use yii\base\Exception;

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
        if ($value instanceof QueryCriteria) {
            return $value;
        }

        $criteria = new QueryCriteria();

        $criteria->query = $this->getQuery([
            'variables' => [
                'element' => $element
            ]
        ]);

        return $criteria;
    }

    /*******************************************
     * VIEWS
     *******************************************/

    /**
     * @inheritdoc
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function getSettingsHtml()
    {
        $query = $this->getQuery();

        if (!$query instanceof DynamicQueryBuilder) {
            throw new Exception("Invalid Salesforce Query Type");
        }

        $settings = new DynamicQuerySettings($query);

        if ($this->hasErrors('query')) {
            $settings->addError('query', $this->getFirstError('query'));
        }

        return Craft::$app->getView()->renderTemplate(
            'force/_components/fieldtypes/Query/settings',
            [
                'field' => $this,
                'querySettings' => $settings
            ]
        );
    }

    /**
     * @inheritdoc
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        return Craft::$app->getView()->renderTemplate(
            'force/_components/fieldtypes/Query/input',
            [
                'element' => $element,
                'value' => $value,
                'field' => $this
            ]
        );
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
