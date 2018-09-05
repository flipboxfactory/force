<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\actions\connections\patron;

use Craft;
use flipbox\patron\Patron;
use flipbox\force\Force;
use flipbox\force\models\Settings;
use flipbox\force\records\Connection;
use flipbox\patron\records\Provider;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce;
use yii\base\Action;
use yii\base\Model;
use flipbox\patron\actions\provider\traits\Populate as PopulateProvider;
use flipbox\patron\actions\provider\traits\Save as SaveProvider;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Save extends Action
{
    use PopulateProvider, SaveProvider;

    /**
     * @inheritdoc
     */
    protected function statusCodeSuccess(): int
    {
        return 201;
    }

    /**
     * HTTP fail response code
     *
     * @return int
     */
    protected function statusCodeFail(): int
    {
        return 400;
    }

    /**
     * @param null $connection
     * @return Connection
     * @throws \flipbox\ember\exceptions\NotFoundException
     */
    protected function resolveConnection($connection = null): Connection
    {
        if($connection instanceof Connection) {
            return $connection;
        }

        if(is_numeric($connection) || is_string($connection)) {
            return Force::getInstance()->getCp()->getConnectionManager()->get($connection);
        }

        return Force::getInstance()->getCp()->getConnectionManager()->create($connection ?: []);
    }

    /**
     * @param Connection $connection
     * @return Provider
     * @throws \flipbox\ember\exceptions\NotFoundException
     */
    protected function resolveProvider(Connection $connection): Provider
    {
        // Get provider from settings
        $provider = $connection->settings['provider'] ?? null;

        if(null !== $provider) {
            return Patron::getInstance()->manageProviders()->get($provider);
        }

        return Patron::getInstance()->manageProviders()->create();
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function handleSuccessResponse($data)
    {
        Craft::$app->getResponse()->setStatusCode($this->statusCodeSuccess());
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function handleFailResponse($data)
    {
        Craft::$app->getResponse()->setStatusCode($this->statusCodeFail());
        return $data;
    }

    /**
     * @param null $connection
     * @return mixed
     * @throws \Exception
     */
    public function run($connection = null)
    {
        return $this->runInternal(
            $this->resolveConnection($connection)
        );
    }

    /**
     * @param Connection $connection
     * @return mixed
     * @throws \Exception
     */
    public function runInternal(Connection $connection)
    {
        $provider = $this->resolveProvider($connection);

        // Populate
        $this->populateProvider($provider);
        $this->populateConnection($connection);

//        // Check access
//        if (($access = $this->checkAccess($connection)) !== true) {
//            return $access;
//        }

        if (!$this->performAction($provider, $connection)) {
            return $this->handleFailResponse($connection);
        }

        return $this->handleSuccessResponse($connection);
    }

    /**
     * @param Connection $connection
     * @return Connection
     */
    public function populateConnection(Connection $connection)
    {
        $attributes = [
            'handle',
            'class',
            'enabled'
        ];

        $values = $this->attributeValuesFromBody($attributes);

        /** @var Connection $connection */
        $connection = Craft::configure(
            $connection,
            $values
        );

        return $connection;
    }

    /**
     * @param Provider $provider
     * @return Provider
     */
    public function populateProvider(Provider $provider)
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
     * @param Provider $provider
     * @param Connection $connection
     * @return bool
     */
    public function performAction(Provider $provider, Connection $connection): bool
    {
        // Db transaction
        $transaction = Craft::$app->getDb()->beginTransaction();

        try {

            // Provider
            if(!$this->saveProvider($provider)) {
                $connection->addError('class', 'Unable to save provider settings');
                $transaction->rollBack();
                return false;
            }

            $settings = $connection->settings;
            $settings['provider'] = $provider->id;

            $connection->settings = $settings;

            if(!$this->saveConnection($connection)) {
                $transaction->rollBack();
                return false;
            }

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        $transaction->commit();
        return true;
    }

    /**
     * @param Connection $connection
     * @return bool
     */
    public function saveConnection(Connection $connection): bool
    {
        return $connection->save();
    }

    /**
     * @param Provider $provider
     * @return bool
     */
    public function saveProvider(Provider $provider): bool
    {
        return $provider->save();
    }
}
