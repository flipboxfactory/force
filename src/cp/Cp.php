<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp;

use Craft;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\View;
use flipbox\force\Force;
use yii\base\Event;
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
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Ember templates
        Event::on(
            View::class,
            View::EVENT_REGISTER_CP_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $e) {
                $e->roots['force/ember/card'] = Craft::$app->getPath()->getVendorPath() .
                    '/flipboxfactory/craft-assets-card/src/templates';
                $e->roots['force/ember/circle-icon'] = Craft::$app->getPath()->getVendorPath() .
                    '/flipboxfactory/craft-assets-circle-icon/src/templates';
            }
        );
    }

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
}
