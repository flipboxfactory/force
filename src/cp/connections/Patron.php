<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\connections;

use Craft;
use flipbox\force\Force;
use flipbox\force\records\Connection;
use flipbox\patron\Patron as PatronPlugin;
use flipbox\patron\records\Provider;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property Force $module
 */
class Patron implements ConnectionTypeInterface
{
    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('force', 'Patron');
    }

    /**
     * @param Connection $connection
     * @return bool
     * @throws \flipbox\ember\exceptions\NotFoundException
     */
    public function process(Connection $connection): bool
    {
        $provider = $this->resolveProvider($connection);

        // Populate
        $this->populateProvider($provider);

        // Provider
        if(!$provider->save()) {
            $connection->addError('class', 'Unable to save provider settings');
            return false;
        }

        $settings = $connection->settings;
        $settings['provider'] = $provider->id;

        $connection->settings = $settings;

        if(!$connection->save()) {
            return false;
        }

        return true;
    }

    /**
     * @param Connection $connection
     * @return Provider
     * @throws \flipbox\ember\exceptions\NotFoundException
     */
    protected function resolveProvider(Connection $connection): Provider
    {
        $manageProviders = PatronPlugin::getInstance()->manageProviders();

        // Get provider from settings
        if(null !== ($provider = $connection->settings['provider'] ?? null)) {
            return $manageProviders->get($provider);
        }

        return $manageProviders->create();
    }

    /**
     * @param Provider $provider
     * @return Provider
     */
    protected function populateProvider(Provider $provider)
    {
        $settings = [
            'clientId',
            'clientSecret',
            'scopes',
            'enabled',
            'environments',
            'settings',
        ];

        $request = Craft::$app->getRequest();

        $class = $request->getBodyParam('class');

        $values = $this->attributeValuesFromBody($settings, 'settings.'.$class.'.');
        $values['class'] = Salesforce::class;

        /** @var Provider $provider */
        $provider = Craft::configure(
            $provider,
            $values
        );

        // Don't change handle
        if ($provider->handle === null) {
            $provider->handle = $request->getBodyParam('handle');
        }

        return $provider;
    }

    /**
     * @param array $attributes
     * @param string|null $prepend
     * @return array
     */
    protected function attributeValuesFromBody(array $attributes, string $prepend = null): array
    {
        $request = Craft::$app->getRequest();

        $values = [];
        foreach ($attributes as $bodyParam => $attribute) {
            if (is_numeric($bodyParam)) {
                $bodyParam = $attribute;
            }
            if (($value = $request->getBodyParam($prepend . $bodyParam)) !== null) {
                $values[$attribute] = $value;
            }
        }

        return $values;
    }

    /**
     * @inheritdoc
     *
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function getSettingsHtml(Connection $connection): string
    {
        $identifier = $connection->settings['provider'] ?? null;

        $providerService = PatronPlugin::getInstance()->manageProviders();

        if ($identifier !== null) {
            $provider = $providerService->find($identifier);
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
