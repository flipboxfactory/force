<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\web\assets\soql;

use craft\web\AssetBundle;
use flipbox\craft\ember\web\assets\actions\Actions;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SOQL extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->js = [
            'js/SOQL' . $this->dotJs()
        ];

        $this->css = [
            'css/SOQL.css'
        ];

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public $sourcePath = __DIR__ . '/dist';

    /**
     * @inheritdoc
     */
    public $depends = [
        Actions::class
    ];
}
