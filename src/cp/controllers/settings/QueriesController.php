<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\controllers\settings;

use Craft;
use craft\helpers\ArrayHelper;
use flipbox\force\actions\queries\Create;
use flipbox\force\actions\queries\Delete;
use flipbox\force\actions\queries\Update;
use flipbox\force\cp\controllers\AbstractController;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class QueriesController extends AbstractController
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
                    'default' => 'query'
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
     * @return array
     */
    protected function verbs(): array
    {
        return [
            'create' => ['post'],
            'update' => ['post', 'put'],
            'delete' => ['post', 'delete']
        ];
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        /** @var Create $action */
        $action = Craft::createObject([
            'class' => Create::class
        ], [
            'create',
            $this
        ]);

        $response = $action->runWithParams([]);

        return $response;
    }

    /**
     * @param null $query
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate($query = null)
    {
        if (null === $query) {
            $query = Craft::$app->getRequest()->getBodyParam('query');
        }

        /** @var Update $action */
        $action = Craft::createObject([
            'class' => Update::class
        ], [
            'update',
            $this
        ]);

        return $action->runWithParams([
            'query' => $query
        ]);
    }

    /**
     * @param null $query
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDelete($query = null)
    {
        if (null === $query) {
            $query = Craft::$app->getRequest()->getBodyParam('query');
        }

        /** @var Delete $action */
        $action = Craft::createObject([
            'class' => Delete::class
        ], [
            'delete',
            $this
        ]);

        return $action->runWithParams([
            'query' => $query
        ]);
    }
}
