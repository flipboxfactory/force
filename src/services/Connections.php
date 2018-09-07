<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services;

use craft\helpers\Json;
use flipbox\ember\helpers\ArrayHelper;
use flipbox\ember\helpers\ObjectHelper;
use flipbox\force\connections\ConnectionInterface;
use flipbox\force\events\RegisterConnectionsEvent;
use flipbox\force\Force;
use flipbox\force\records\Connection;
use yii\base\InvalidConfigException;
use yii\di\ServiceLocator;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Connections extends ServiceLocator
{
    /**
     * @event RegisterConnectionsEvent The event that is triggered when registering connections.
     */
    const EVENT_REGISTER_CONNECTIONS = 'registerConnections';

    /**
     * The default connection handle
     */
    const APP_CONNECTION = 'app';

    /**
     * The default connection identifier
     */
    const DEFAULT_CONNECTION = 'DEFAULT';

    /**
     * @return string
     */
    public static function objectClassInstance()
    {
        return ConnectionInterface::class;
    }

    /**
     * @inheritdoc
     */
    public static function recordClass(): string
    {
        return Connection::class;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $event = new RegisterConnectionsEvent([
            'connections' => $this->dbConnections()
        ]);

        $this->trigger(self::EVENT_REGISTER_CONNECTIONS, $event);

        $this->setComponents(
            $event->connections
        );
    }

    /**
     * @return ConnectionInterface[]
     */
    private function dbConnections(): array
    {
        $configs = Force::getInstance()->getConnectionManager()->getQuery([
            'asArray' => true,
            'indexBy' => 'handle'
        ])->all();

        $connections = [];

        foreach ($configs as $key => $config) {
            try {
                $connections[$key] = ObjectHelper::create(
                    $this->prepareConfigSettings($config),
                    ConnectionInterface::class
                );
            } catch (\Exception $e) {
                Force::warning(sprintf(
                    "Unable to register connection '%s' due to '%s",
                    $key,
                    $e->getMessage()
                ));
            }
        }

        return $connections;
    }

    /**
     * @inheritdoc
     * @return ConnectionInterface
     */
    public function get($id, $throwException = true): ConnectionInterface
    {
        if ($id === self::DEFAULT_CONNECTION) {
            $id = Force::getInstance()->getSettings()->getDefaultConnection();
        }

        $connection = parent::get($id, $throwException);

        if (!$connection instanceof ConnectionInterface) {
            throw new InvalidConfigException(sprintf(
                "Connection '%s' must be an instance of '%s', '%s' given.",
                (string)$id,
                ConnectionInterface::class,
                get_class($connection)
            ));
        }
        return $connection;
    }

    /**
     * @param bool $throwException
     * @return ConnectionInterface[]
     * @throws InvalidConfigException
     */
    public function getAll($throwException = true): array
    {
        $components = [];

        foreach ($this->getComponents(true) as $id => $component) {
            $components[$id] = $this->get($id, $throwException);
        }

        return $components;
    }

    /**
     * @param array $config
     * @return array
     */
    private function prepareConfigSettings(array $config = []): array
    {
        // Handle settings
        $settings = ArrayHelper::remove($config, 'settings');

        if (is_string($settings)) {
            $settings = Json::decodeIfJson($settings);
        }

        if (!is_array($settings) || empty($settings)) {
            return $config;
        }

        return array_merge($config, $settings);
    }
}
