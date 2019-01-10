<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\cp\controllers;

use Craft;
use craft\helpers\ArrayHelper;
use flipbox\craft\salesforce\actions\query\CreateQuery;
use flipbox\craft\salesforce\actions\query\DeleteQuery;
use flipbox\craft\salesforce\actions\query\PreviewQuery;
use flipbox\craft\salesforce\actions\query\UpdateQuery;

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
                            201 => Craft::t('salesforce', "Query successfully created."),
                            400 => Craft::t('salesforce', "Failed to create query.")
                        ],
                        'update' => [
                            200 => Craft::t('salesforce', "Query successfully updated."),
                            400 => Craft::t('salesforce', "Failed to update query.")
                        ],
                        'delete' => [
                            204 => Craft::t('salesforce', "Query successfully deleted."),
                            400 => Craft::t('salesforce', "Failed to delete query.")
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
        /** @var CreateQuery $action */
        $action = Craft::createObject([
            'class' => CreateQuery::class
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

        /** @var UpdateQuery $action */
        $action = Craft::createObject([
            'class' => UpdateQuery::class
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

        /** @var DeleteQuery $action */
        $action = Craft::createObject([
            'class' => DeleteQuery::class
        ], [
            'delete',
            $this
        ]);

        return $action->runWithParams([
            'query' => $query
        ]);
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRequest()
    {
        /** @var PreviewQuery $action */
        return (Craft::createObject([
            'class' => PreviewQuery::class
        ], [
            'dissociate',
            $this
        ]))->runWithParams([]);
    }
}
