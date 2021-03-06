<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\search;

use craft\helpers\ArrayHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class DynamicSearchBuilder extends RawSearchBuilder
{
    /**
     * The opening variable character
     */
    const VARIABLE_OPENING = '{';

    /**
     * The closing variable character
     */
    const VARIABLE_CLOSING = '}';

    /**
     * @var array
     */
    public $variables = [];

    /**
     * @inheritdoc
     */
    public function build(): string
    {
        return $this->prepareSearch($this->search);
    }

    /**
     * @return array
     */
    protected function getVariables(): array
    {
        if (!array($this->variables)) {
            $this->variables = ['variable' => $this->variables];
        }

        return $this->variables;
    }

    /**
     * @param string $soql
     * @return string
     */
    private function prepareSearch(string $soql): string
    {
        if (false === (preg_match_all(
            '/' . self::VARIABLE_OPENING . '(.*?)' . self::VARIABLE_CLOSING . '/',
            $soql,
            $matches
        ))) {
            return $soql;
        }

        $replace = $this->getReplacingAttributes($matches[1]);

        return str_ireplace(array_keys($replace), array_values($replace), $soql);
    }

    /**
     * @param array $variables
     * @return array
     */
    private function getReplacingAttributes(array $variables = [])
    {
        $attributes = $this->getVariables();

        $values = [];

        foreach ($variables as $variable) {
            $values[self::VARIABLE_OPENING . $variable . self::VARIABLE_CLOSING] = ArrayHelper::getValue(
                $attributes,
                $variable,
                $variable
            );
        }

        return $values;
    }

    /**
     * @return array
     */
    public function toConfig(): array
    {
        return array_merge(
            parent::toConfig(),
            [
                'variables' => $this->getVariables()
            ]
        );
    }
}
