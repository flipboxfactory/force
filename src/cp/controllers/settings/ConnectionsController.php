<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\controllers\settings;

use Craft;
use craft\helpers\ArrayHelper;
use flipbox\force\cp\Cp;
use flipbox\force\Force;
use flipbox\force\cp\controllers\AbstractController;

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
                            201 => Craft::t('force', "Query successfully created."),
                            400 => Craft::t('force', "Failed to create query.")
                        ],
                        'update' => [
                            200 => Craft::t('force', "Query successfully updated."),
                            400 => Craft::t('force', "Failed to update query.")
                        ],
                        'delete' => [
                            204 => Craft::t('force', "Query successfully deleted."),
                            400 => Craft::t('force', "Failed to delete query.")
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
        $class = Craft::$app->getRequest()->getRequiredBodyParam('class');

        $provider = $this->module->getConnectionManager()->getType(
            $class
        );

        /** @var \yii\base\Action $action */
        $action = Craft::createObject([
            'class' => $provider::actionClass()
        ], [
            'create',
            $this
        ]);

        return $action->runWithParams([]);
    }

    /**
     * @param null $connection
     * @return mixed
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionUpdate($connection = null)
    {
        $class = Craft::$app->getRequest()->getRequiredBodyParam('class');

        if (null === $connection) {
            $connection = Craft::$app->getRequest()->getBodyParam('connection');
        }

        $provider = $this->module->getConnectionManager()->getType(
            $class
        );

        /** @var \yii\base\Action $action */
        $action = Craft::createObject([
            'class' => $provider::actionClass()
        ], [
            'update',
            $this
        ]);

        return $action->runWithParams([
            'connection' => $connection
        ]);
    }
}
