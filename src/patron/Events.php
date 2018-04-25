<?php

namespace flipbox\force\patron;

use flipbox\force\patron\providers\SalesforceSettings;
use flipbox\patron\cp\Cp;
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
    }
}
