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
interface SearchBuilderInterface
{
    /**
     * @return array
     */
    public function toConfig(): array;

    /**
     * @return string
     */
    public function build(): string;

    /**
     * @return string
     */
    public function __toString();
}
