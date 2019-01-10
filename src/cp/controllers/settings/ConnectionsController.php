<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\cp\controllers\settings;

use Craft;
use craft\helpers\ArrayHelper;
use flipbox\craft\salesforce\actions\connections\CreateConnection;
use flipbox\craft\salesforce\actions\connections\DeleteConnection;
use flipbox\craft\salesforce\actions\connections\UpdateConnection;
use flipbox\craft\salesforce\cp\controllers\AbstractController;
use flipbox\craft\salesforce\cp\Cp;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property Cp $module
 */
class ConnectionsController extends AbstractController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'error' => [
                    'default' => 'connection'
                ],
                'redirect' => [
                    'only' => ['create', 'update', 'delete'],
                    'actions' => [
                        'create' => [201],
                        'update' => [200],
                        'delete' => [204],
                    ]
                ],
                'flash' => [
                    'actions' => [
                        'create' => [
                            201 => Craft::t('salesforce', "Connection successfully created."),
                            400 => Craft::t('salesforce', "Failed to create connection.")
                        ],
                        'update' => [
                            200 => Craft::t('salesforce', "Connection successfully updated."),
                            400 => Craft::t('salesforce', "Failed to update connection.")
                        ],
                        'delete' => [
                            204 => Craft::t('salesforce', "Connection successfully deleted."),
                            400 => Craft::t('salesforce', "Failed to delete connection.")
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        /** @var \yii\base\Action $action */
        $action = Craft::createObject([
            'class' => CreateConnection::class
        ], [
            'update',
            $this
        ]);

        return $action->runWithParams([]);
    }

    /**
     * @param null $connection
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate($connection = null)
    {
        if (null === $connection) {
            $connection = Craft::$app->getRequest()->getBodyParam('connection');
        }

        /** @var \yii\base\Action $action */
        $action = Craft::createObject([
            'class' => UpdateConnection::class
        ], [
            'update',
            $this
        ]);

        return $action->runWithParams([
            'connection' => $connection
        ]);
    }

    /**
     * @param null $connection
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDelete($connection = null)
    {
        if (null === $connection) {
            $connection = Craft::$app->getRequest()->getBodyParam('connection');
        }

        /** @var DeleteConnection $action */
        $action = Craft::createObject([
            'class' => DeleteConnection::class
        ], [
            'delete',
            $this
        ]);

        return $action->runWithParams([
            'connection' => $connection
        ]);
    }
}
