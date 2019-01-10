<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\actions\query;

use Craft;
use flipbox\craft\salesforce\records\SOQL;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait PopulateQueryTrait
{
    /**
     * @param SOQL $query
     * @return SOQL
     */
    protected function populateSettings(SOQL $query): SOQL
    {
        $settings = Craft::$app->getRequest()->getBodyParam('settings', []);

        if (!is_array($settings)) {
            $settings = [$settings];
        }

        $settings['variables'] = $this->prepareVariables((array)($settings['variables'] ?? []));

        $query->settings = $settings;
        return $query;
    }

    /**
     * @param array $variables
     * @return array
     */
    protected function prepareVariables(array $variables): array
    {
        $return = [];

        foreach (array_filter($variables) as $key => $value) {
            if (is_numeric($key)) {
                $key = $value['key'] ?? $key;
                $value = $value['value'] ?? $value;
            }

            $return[$key] = $value;
        }

        return $return;
    }
}
