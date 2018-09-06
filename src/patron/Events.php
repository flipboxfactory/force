<?php

namespace flipbox\force\patron;

use flipbox\force\events\RegisterConnectionConfigurationsEvent;
use flipbox\force\patron\connections\AccessTokenConnection;
use flipbox\force\patron\connections\AccessTokenConnectionConfiguration;
use flipbox\force\patron\providers\SalesforceSettings;
use flipbox\force\services\ConnectionManager;
use flipbox\patron\cp\Cp;
use flipbox\patron\events\RegisterProviderIcon;
use flipbox\patron\events\RegisterProviders;
use flipbox\patron\events\RegisterProviderSettings;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce as SalesforceProvider;
use yii\base\Event;

class Events
{
    /**
     * Register events
     */
    public static function register()
    {
        // OAuth2 Provider
        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_PROVIDERS,
            function (RegisterProviders $event) {
                $event->providers[] = SalesforceProvider::class;
            }
        );

        // OAuth2 Provider Settings
        RegisterProviderSettings::on(
            SalesforceProvider::class,
            RegisterProviderSettings::REGISTER_SETTINGS,
            function (RegisterProviderSettings $event) {
                $event->class = SalesforceSettings::class;
            }
        );

        // OAuth2 Provider Icon
        RegisterProviderIcon::on(
            SalesforceProvider::class,
            RegisterProviderIcon::REGISTER_ICON,
            function (RegisterProviderIcon $event) {
                $event->icon = '@vendor/flipboxfactory/force/src/icons/salesforce.svg';
            }
        );

        // Configuration
        Event::on(
            ConnectionManager::class,
            ConnectionManager::EVENT_REGISTER_CONFIGURATIONS,
            function (RegisterConnectionConfigurationsEvent $event) {
                $event->configurations[AccessTokenConnection::class] = AccessTokenConnectionConfiguration::class;
            }
        );
    }
}
