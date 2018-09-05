<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\patron\providers;

use Craft;
use flipbox\ember\helpers\ModelHelper;
use flipbox\patron\providers\Base;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SalesforceSettings extends Base
{
    /**
     * @var string
     */
    public $domain;

    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function inputHtml(): string
    {
        return Craft::$app->getView()->renderTemplate(
            'force/_patron/providers/settings',
            [
                'settings' => $this
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    [
                        'domain'
                    ],
                    'required'
                ],
                [
                    [
                        'domain'
                    ],
                    'url'
                ],
                [
                    [
                        'domain'
                    ],
                    'safe',
                    'on' => [
                        ModelHelper::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );
    }
}
