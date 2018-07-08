<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\search;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class RawSearchBuilder extends AbstractSearchBuilder
{
    /**
     * The soql query
     *
     * @var string
     */
    public $search;

    /**
     * @inheritdoc
     */
    public function build(): string
    {
        return (string)$this->search;
    }

    /**
     * @return array
     */
    public function toConfig(): array
    {
        return array_merge(
            parent::toConfig(),
            [
                'soql' => $this->search
            ]
        );
    }
}
