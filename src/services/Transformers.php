<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services;

use flipbox\flux\Flux;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Transformers extends Component
{
    /**
     * The scope
     */
    const FORCE_SCOPE = 'force';

    /**
     * @param string $identifier
     * @param string $class
     * @param null $default
     * @return callable|null
     */
    public function find(
        string $identifier,
        string $class,
        $default = null
    ) {
        return Flux::getInstance()->getTransformers()->find(
            $identifier,
            static::FORCE_SCOPE,
            $class,
            $default
        );
    }
}
