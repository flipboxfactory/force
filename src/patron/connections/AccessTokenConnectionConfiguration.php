<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\patron\connections;

use Craft;
use flipbox\craft\integration\connections\DefaultConfiguration;
use flipbox\craft\integration\records\IntegrationConnection;
use flipbox\force\Force;
use flipbox\force\records\Connection;
use flipbox\patron\Patron as PatronPlugin;
use flipbox\patron\records\Provider;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce;

/**
 * This class is a configurable wrapper to a Connection.  It's used in the CP.
 *
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
     * @return IntegrationConnection|Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @return bool
     * @throws \flipbox\ember\exceptions\NotFoundException
     */
    public function save(): bool
    {
        $provider = $this->getProvider();

        // Populate
        $this->populateProvider($provider);

        // Sync the enabled status
        $provider->enabled = $this->connection->enabled;

        // Base settings
        $this->connection->settings = array_merge(
            $this->connection->settings,
            $this->attributeValuesFromBody(['version'])
        );

        // Provider
        if (!$provider->saveAndLock(Force::getInstance())) {
            $this->connection->addError('class', 'Unable to save provider settings');
            return false;
        }

        // Add provider to settings
        $this->connection->settings = array_merge(
            $this->connection->settings,
            [
                'provider' => $provider->id
            ]
        );

        return parent::save();
    }

    /**
     * @return bool
     * @throws \flipbox\ember\exceptions\NotFoundException
     */
    public function delete(): bool
    {
        $provider = $this->getProvider();

        // Provider
        if (!$provider->delete(Force::getInstance())) {
            $this->connection->addError('class', 'Unable to delete provider settings');
            return false;
        }

        return parent::delete();
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
                $provider = $manageProviders->find($provider);
            }

            if (!$provider instanceof Provider) {
                $provider = $manageProviders->create();
            }

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

        /** @var Provider $provider */
        $provider = Craft::configure(
            $provider,
            $this->attributeValuesFromBody($settings)
        );

        // Don't change handle
        if ($provider->handle === null) {
            $provider->handle = Craft::$app->getRequest()->getBodyParam('handle');
        }

        return $provider;
    }

    /**
     * @param array $attributes
     * @param string|null $prepend
     * @return array
     */
    protected function attributeValuesFromBody(array $attributes, string $prepend = 'settings.'): array
    {
        $request = Craft::$app->getRequest();

        $prepend .= $this->connection->class . '.';

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
            'force/_patron/connections/configuration',
            [
                'provider' => $this->getProvider(),
                'configuration' => $this
            ]
        );
    }
}
