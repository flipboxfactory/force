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
     * @return Provider
     */
    protected function newProvider(array $config = []): Provider
    {
        return Patron::getInstance()->manageProviders()->create($config);
    }

    /**
     * @inheritdoc
     * @return Provider
     */
    protected function newConnection(array $config = []): Connection
    {
        return Force::getInstance()->getCp()->getConnectionManager()->create($config);
    }


    /**
     * @inheritdoc
     */
    public function statusCodeSuccess(): int
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
     * @param $data
     * @return mixed
     */
    protected function handleSuccessResponse($data)
    {
        // Success status code
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
     * @inheritdoc
     */
    public function run()
    {
        return $this->runInternal(
            $this->newProvider(),
            $this->newConnection()
        );
    }

    /**
     * @inheritdoc
     * @param ActiveRecord $record
     */
    public function runInternal(Provider $provider, Connection $connection)
    {
        // Populate
        $this->populateProvider($provider);
        $this->populateConnection($connection);

//        // Check access
//        if (($access = $this->checkAccess($provider, $connection)) !== true) {
//            return $access;
//        }

        if (!$this->performAction($provider, $connection)) {

            var_dump($connection->getErrors());
            exit;

            return $this->handleFailResponse($connection);
        }




        return $this->handleSuccessResponse($connection);
    }

    /**
     * @return array
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
        $attributes = [
            'handle',
            'clientId',
            'clientSecret',
            'scopes',
            'enabled',
            'environments'
        ];

        $request = Craft::$app->getRequest();

        $values = array_merge(
            $this->attributeValuesFromBody($attributes),
            $request->getBodyParam('settings')[$request->getBodyParam('class')] ?? []
        );
        $values['class'] = Salesforce::class;

        /** @var Provider $provider */
        $provider = Craft::configure(
            $provider,
            $values
        );

        return $provider;
    }

    /**
     * @param BaseObject $object
     * @return BaseObject
     */
    protected function populate(BaseObject $object): BaseObject
    {
        // Valid attribute values
        $attributes = $this->attributeValuesFromBody();

        /** @var BaseObject $object */
        $object = Craft::configure(
            $object,
            $attributes
        );

        return $object;
    }

    /**
     * @return array
     */
    protected function attributeValuesFromBody(array $attributes): array
    {
        $request = Craft::$app->getRequest();

        $values = [];
        foreach ($attributes as $bodyParam => $attribute) {
            if (is_numeric($bodyParam)) {
                $bodyParam = $attribute;
            }
            if (($value = $request->getBodyParam($bodyParam)) !== null) {
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
            $settings['provider'] = $provider->handle;;

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
