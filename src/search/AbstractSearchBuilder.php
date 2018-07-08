<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\search;

use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class AbstractSearchBuilder extends BaseObject implements SearchBuilderInterface
{
    /**
     * @return mixed|string
     */
    public function __toString()
    {
        return $this->build();
    }

    /**
     * @return array
     */
    public function toConfig(): array
    {
        return [
            'class' => get_class($this)
        ];
    }
}
