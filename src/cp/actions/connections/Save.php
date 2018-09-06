<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\actions\connections;

use Craft;
use flipbox\ember\actions\traits\CheckAccess;
use flipbox\force\cp\connections\ConnectionTypeInterface;
use flipbox\force\Force;
use flipbox\force\records\Connection;
use flipbox\patron\actions\provider\traits\Populate as PopulateProvider;
use flipbox\patron\actions\provider\traits\Save as SaveProvider;
use yii\base\Action;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Save extends Action
{
    use PopulateProvider, SaveProvider, CheckAccess;

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
     * @param string $type
     * @param null $connection
     * @return mixed
     * @throws \Exception
     * @throws \flipbox\ember\exceptions\ObjectNotFoundException
     */
    public function run(string $type, $connection = null)
    {
        $connectionManager = Force::getInstance()->getCp()->getConnectionManager();

        return $this->runInternal(
            $connectionManager->getType($type),
            $connectionManager->get($connection)
        );
    }

    /**
     * @param ConnectionTypeInterface $type
     * @param Connection $connection
     * @return mixed
     * @throws \Exception
     */
    protected function runInternal(ConnectionTypeInterface $type, Connection $connection)
    {
        $this->populateConnection($connection);

        // Check access
        if (($access = $this->checkAccess($connection)) !== true) {
            return $access;
        }

        if (!$this->performAction($type, $connection)) {
            return $this->handleFailResponse($connection);
        }

        return $this->handleSuccessResponse($connection);
    }

    /**
     * @param Connection $connection
     * @return Connection
     */
    protected function populateConnection(Connection $connection)
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
     * @param array $attributes
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
     * @param ConnectionTypeInterface $type
     * @param Connection $connection
     * @return bool
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    protected function performAction(ConnectionTypeInterface $type, Connection $connection): bool
    {
        // Db transaction
        $transaction = Craft::$app->getDb()->beginTransaction();

        try {
            if (!$type->process($connection)) {
                $connection->addError('class', 'Unable to save provider settings');
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
}
