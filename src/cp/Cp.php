<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\cp;

use Craft;
use flipbox\craft\salesforce\events\RegisterConnectionsEvent;
use flipbox\craft\salesforce\Force;
use yii\base\Module as BaseModule;
use yii\web\NotFoundHttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property Force $module
 */
class Cp extends BaseModule
{
    private $registeredConnections;


    /**
     * @inheritdoc
     * @throws NotFoundHttpException
     */
    public function beforeAction($action)
    {
        if (!Craft::$app->request->getIsCpRequest()) {
            throw new NotFoundHttpException();
        }

        return parent::beforeAction($action);
    }

    /*******************************************
     * PROVIDERS
     *******************************************/

    /**
     * @return array
     */
    public function getAvailableConnections(): array
    {
        if ($this->registeredConnections === null) {
            $event = new RegisterConnectionsEvent();

            $this->trigger(
                $event::REGISTER_CONNECTIONS,
                $event
            );

            $this->registeredConnections = $event->connections;
        }

        return $this->registeredConnections;
    }
}
