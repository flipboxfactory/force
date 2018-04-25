<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\actions;

use Craft;
use flipbox\ember\actions\model\traits\Save;
use flipbox\force\Force;
use flipbox\force\models\Settings;
use yii\base\Action;
use yii\base\Model;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SaveSettings extends Action
{
    use Save;

    /**
     * @return array
     */
    protected function validBodyParams(): array
    {
        return [
            'instanceUrl',
            'sObjectViewUrlString',
            'sObjectListUrlString'
        ];
    }

    /**
     *
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
}