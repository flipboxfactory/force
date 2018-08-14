<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\connections;

use Craft;
use flipbox\force\cp\actions\connections\patron\Save;
use flipbox\force\Force;
use flipbox\force\records\Connection;
use flipbox\patron\Patron as PatronPlugin;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property Force $module
 */
class Patron
{
    /**
     * @return string
     */
    public static function displayName(): string
    {
        return Craft::t('force', 'Patron');
    }

    /**
     * @return string
     */
    public static function actionClass(): string
    {
        return Save::class;
    }

    /**
     * @param Connection $connection
     * @return string
     */
    public function getSettingsHtml(Connection $connection): string
    {
        $handle = $connection->settings['provider'] ?? null;

        $providerService = PatronPlugin::getInstance()->manageProviders();

        if($handle !== null) {
            $provider = $providerService->find($handle);
        }

        if (empty($provider)) {
            $provider = $providerService->create([
                'class' => Salesforce::class
            ]);
        }

        return Craft::$app->view->renderTemplate(
            'force/_cp/settings/connections/types/Patron',
            [
                'provider' => $provider
            ]
        );
    }
}
