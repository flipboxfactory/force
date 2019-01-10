<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\actions;

use Craft;
use flipbox\force\Force;
use flipbox\force\models\Settings;
use yii\base\Model;
use flipbox\craft\ember\actions\models\CreateModel;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SaveSettings extends CreateModel
{
    /**
     * @return array
     */
    public $validBodyParams = [
        'defaultConnection',
        'defaultCache'
    ];

    /**
     * @inheritdoc
     */
    public $statusCodeSuccess = 200;

    /**
     * @inheritdoc
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function run()
    {
        return $this->runInternal(
            Force::getInstance()->getSettings()
        );
    }

    /**
     * @inheritdoc
     * @param Settings $model
     */
    protected function performAction(Model $model): bool
    {
        return Craft::$app->getPlugins()->savePluginSettings(
            Force::getInstance(),
            $model->toArray(
                $this->validBodyParams()
            )
        );
    }

    /**
     * @inheritdoc
     * @return Settings
     */
    protected function newModel(array $config = []): Model
    {
        return clone Force::getInstance()->getSettings();
    }
}
