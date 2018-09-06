<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\controllers\settings;

use Craft;
use craft\helpers\ArrayHelper;
use flipbox\force\cp\actions\connections\Save;
use flipbox\force\cp\controllers\AbstractController;
use flipbox\force\cp\Cp;

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
                            201 => Craft::t('force', "Connection successfully created."),
                            400 => Craft::t('force', "Failed to create connection.")
                        ],
                        'update' => [
                            200 => Craft::t('force', "Connection successfully updated."),
                            400 => Craft::t('force', "Failed to update connection.")
                        ],
                        'delete' => [
                            204 => Craft::t('force', "Connection successfully deleted."),
                            400 => Craft::t('force', "Failed to delete connection.")
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * @param null $type
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionCreate($type = null)
    {
        if (null === $type) {
            $type = Craft::$app->getRequest()->getRequiredBodyParam('class');
        }

        /** @var \yii\base\Action $action */
        $action = Craft::createObject([
            'class' => Save::class
        ], [
            'update',
            $this
        ]);

        return $action->runWithParams([
            'type' => $type
        ]);
    }

    /**
     * @param null $type
     * @param null $connection
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionUpdate($type = null, $connection = null)
    {
        if (null === $type) {
            $type = Craft::$app->getRequest()->getRequiredBodyParam('class');
        }

        if (null === $connection) {
            $connection = Craft::$app->getRequest()->getBodyParam('connection');
        }

        /** @var \yii\base\Action $action */
        $action = Craft::createObject([
            'class' => Save::class
        ], [
            'update',
            $this
        ]);

        return $action->runWithParams([
            'type' => $type,
            'connection' => $connection
        ]);
    }
}
