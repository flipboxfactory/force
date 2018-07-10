<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\web\assets\objects;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;
use flipbox\ember\web\assets\actions\Actions;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SObjects extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->js = [
            'js/SObjects' . $this->dotJs()
        ];
        $this->css = [
            'css/SObjects.css'
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
        CpAsset::class,
        Actions::class
    ];
}
