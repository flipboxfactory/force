<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\controllers;

use Craft;
use flipbox\force\Force;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class QueriesController extends AbstractController
{
    /**
     * @return mixed
     */
    public function actionRequest()
    {
        $criteria = Force::getInstance()->getResources()->getQuery()->getCriteria(
            Craft::$app->getRequest()->getBodyParams()
        );

        return $criteria->fetch();
    }
}
