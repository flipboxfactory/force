<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\controllers;

use craft\helpers\ArrayHelper;
use flipbox\ember\filters\FlashMessageFilter;
use flipbox\ember\filters\ModelErrorFilter;
use flipbox\ember\filters\RedirectFilter;
use flipbox\force\cp\Cp;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property Cp $module
 */
abstract class AbstractController extends \flipbox\ember\controllers\AbstractController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'redirect' => [
                    'class' => RedirectFilter::class
                ],
                'error' => [
                    'class' => ModelErrorFilter::class
                ],
                'flash' => [
                    'class' => FlashMessageFilter::class
                ]
            ]
        );
    }
}
