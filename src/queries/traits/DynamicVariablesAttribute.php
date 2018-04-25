<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\queries\traits;

use craft\helpers\ArrayHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait DynamicVariablesAttribute
{
    /**
     * @var array
     */
    private $variables = [];

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @param array $variables
     * @return $this
     */
    public function setVariables(array $variables = [])
    {
        if (!ArrayHelper::isAssociative($variables)) {
            $variables = ArrayHelper::map($variables, 'key', 'value');
        }

        foreach ($variables as $key => $variable) {
            $this->variables[$key] = $variable;
        }

        return $this;
    }
}
