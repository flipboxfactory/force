<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/salesforce/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/salesforce
 */

namespace flipbox\force\query;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface QueryBuilderInterface
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
