<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services;

use Craft;
use craft\base\ElementInterface;
use flipbox\force\criteria\QueryCriteria;
use flipbox\force\fields\Query;
use flipbox\force\Force;
use flipbox\force\query\DynamicQueryBuilder;
use flipbox\force\query\settings\DynamicQuerySettings;
use yii\base\Component;
use yii\base\Exception;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class QueryField extends Component
{
    /**
     * @inheritdoc
     * @return QueryCriteria
     */
    public function normalizeValue(
        Query $field,
        $value,
        ElementInterface $element = null
    ) {
        if ($value instanceof QueryCriteria) {
            return $value;
        }

        return Force::getInstance()->getResources()->getQuery()->getCriteria([
            'query' => $field->getQuery([
                'variables' => [
                    'element' => $element
                ]
            ])
        ]);
    }

    /**
     * @param Query $field
     * @return DynamicQuerySettings
     * @throws Exception
     */
    protected function getQuerySettings(Query $field)
    {
        $query = $field->getQuery();

        if (!$query instanceof DynamicQueryBuilder) {
            throw new Exception("Invalid Query Type");
        }

        $settings = new DynamicQuerySettings($query);

        if ($field->hasErrors('query')) {
            $settings->addError('query', $field->getFirstError('query'));
        }

        return $settings;
    }

    /**
     * @param Query $field
     * @return null|string
     * @throws Exception
     * @throws \Twig_Error_Loader
     */
    public function getSettingsHtml(
        Query $field
    ) {
        return Craft::$app->getView()->renderTemplate(
            'force/_components/fieldtypes/Query/settings',
            [
                'field' => $field,
                'querySettings' => $this->getQuerySettings($field)
            ]
        );
    }

    /**
     * @param Query $field
     * @param QueryCriteria $value
     * @param ElementInterface $element
     * @return string
     * @throws Exception
     * @throws \Twig_Error_Loader
     */
    public function getInputHtml(
        Query $field,
        QueryCriteria $value,
        ElementInterface $element
    ) {
        return Craft::$app->getView()->renderTemplate(
            'force/_components/fieldtypes/Query/input',
            [
                'element' => $element,
                'value' => $value,
                'field' => $field
            ]
        );
    }
}
