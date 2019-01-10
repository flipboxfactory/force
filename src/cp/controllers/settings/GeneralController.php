<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\cp\controllers\settings;

use Craft;
use craft\helpers\ArrayHelper;
use flipbox\craft\salesforce\cp\actions\SaveSettings;
use flipbox\craft\salesforce\cp\controllers\AbstractController;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class GeneralController extends AbstractController
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
                    'default' => 'settings'
                ],
                'redirect' => [
                    'only' => ['save'],
                    'actions' => [
                        'save' => [200]
                    ]
                ],
                'flash' => [
                    'actions' => [
                        'save' => [
                            200 => Craft::t('salesforce', "Settings successfully saved."),
                            400 => Craft::t('salesforce', "Failed to save settings.")
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
            'save' => ['post']
        ];
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSave()
    {
        /** @var SaveSettings $action */
        $action = Craft::createObject([
            'class' => SaveSettings::class
        ], [
            'save',
            $this
        ]);

        return $action->runWithParams([]);
    }
}
