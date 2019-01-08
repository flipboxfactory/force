<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\controllers;

use Craft;
use Flipbox\Salesforce\Criteria\ObjectAccessorCriteria;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class QueriesController extends AbstractController
{
    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRequest()
    {
        $criteria = new ObjectAccessorCriteria(
            Craft::$app->getRequest()->getBodyParams()
        );

        return $criteria->read();
    }
}
