<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\patron\connections;

use Craft;
use flipbox\force\connections\DefaultConfiguration;
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
class AccessTokenConnectionConfiguration extends DefaultConfiguration
{
    /**
     * @var Connection
     */
    private $provider;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('force', 'Patron');
    }

    /**
     * @return bool
     * @throws \flipbox\ember\exceptions\NotFoundException
     */
    public function process(): bool
    {
        $provider = $this->getProvider();

        // Populate
        $this->populateProvider($provider);

        // Provider
        if (!$provider->save()) {
            $this->connection->addError('class', 'Unable to save provider settings');
            return false;
        }

        $settings = $this->connection->settings;
        $settings['provider'] = $provider->id;

        $this->connection->settings = $settings;

        return parent::process();
    }

    /**
     * @return Provider
     * @throws \flipbox\ember\exceptions\NotFoundException
     */
    protected function getProvider(): Provider
    {
        if ($this->provider === null) {
            $manageProviders = PatronPlugin::getInstance()->manageProviders();

            // Get provider from settings
            if (null !== ($provider = $this->connection->settings['provider'] ?? null)) {
                if (null === ($provider = $manageProviders->get($provider))) {
                    $provider = $manageProviders->create();
                }
            }

            // Always
            $provider->class = Salesforce::class;

            $this->provider = $provider;
        }

        return $this->provider;
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

        $values = $this->attributeValuesFromBody($settings, 'settings.' . $class . '.');


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
    public function getSettingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'force/_cp/settings/connections/types/Patron',
            [
                'provider' => $this->getProvider()
            ]
        );
    }
}
