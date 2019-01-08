<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\query;

use Craft;
use flipbox\force\records\SOQL;

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
    private function populateSettings(SOQL $query): SOQL
    {
        $query->settings = [
            'query' => $this->getQuerySettings()
        ];
        return $query;
    }

    /**
     * @return array
     */
    private function getQuerySettings(): array
    {
        $settings = Craft::$app->getRequest()->getBodyParam('settings.query', []);

        if (!is_array($settings)) {
            $settings = [$settings];
        }

        return $settings;
    }
}
