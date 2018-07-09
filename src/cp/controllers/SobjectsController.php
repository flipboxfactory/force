<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\controllers;

use Craft;
use craft\helpers\ArrayHelper;
use flipbox\force\actions\sobjects\Associate;
use flipbox\force\actions\sobjects\Dissociate;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SobjectsController extends AbstractController
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
                    'default' => 'element'
                ],
                'redirect' => [
                    'only' => ['associate', 'dissociate'],
                    'actions' => [
                        'associate' => [200],
                        'dissociate' => [200]
                    ]
                ],
                'flash' => [
                    'actions' => [
                        'associate' => [
                            200 => Craft::t('force', "Salesforce Object associated successfully"),
                            400 => Craft::t('force', "Failed to associate Salesforce Object")
                        ],
                        'dissociate' => [
                            200 => Craft::t('force', "Salesforce Object dissociated successfully"),
                            400 => Craft::t('force', "Failed to dissociate Salesforce Object")
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * @param string|null $sObjectId
     * @param string|null $field
     * @param string|null $element
     * @return \flipbox\force\records\ObjectAssociation|null
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionAssociate(
        string $sObjectId = null,
        string $field = null,
        string $element = null
    ) {

        if ($sObjectId === null) {
            $sObjectId = Craft::$app->getRequest()->getRequiredParam('sObjectId');
        }

        if ($field === null) {
            $field = Craft::$app->getRequest()->getRequiredParam('field');
        }

        if ($element === null) {
            $element = Craft::$app->getRequest()->getRequiredParam('element');
        }

        /** @var Associate $action */
        return (Craft::createObject([
            'class' => Associate::class
        ], [
            'associate',
            $this
        ]))->runWithParams([
            'field' => $field,
            'element' => $element,
            'sObjectId' => $sObjectId
        ]);
    }

    /**
     * @param string|null $sObjectId
     * @param string|null $field
     * @param string|null $element
     * @return \flipbox\force\records\ObjectAssociation|null
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionDissociate(
        string $sObjectId = null,
        string $field = null,
        string $element = null
    ) {

        if ($sObjectId === null) {
            $sObjectId = Craft::$app->getRequest()->getRequiredParam('sObjectId');
        }

        if ($field === null) {
            $field = Craft::$app->getRequest()->getRequiredParam('field');
        }

        if ($element === null) {
            $element = Craft::$app->getRequest()->getRequiredParam('element');
        }

        /** @var Dissociate $action */
        return (Craft::createObject([
            'class' => Dissociate::class
        ], [
            'dissociate',
            $this
        ]))->runWithParams([
            'field' => $field,
            'element' => $element,
            'sObjectId' => $sObjectId
        ]);
    }
}
