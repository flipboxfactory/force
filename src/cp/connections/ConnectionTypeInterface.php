<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\connections;

use flipbox\force\records\Connection;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface ConnectionTypeInterface
{
    /**
     * @return string
     */
    public static function displayName(): string;

    /**
     * Process / Save a connection (and preform any additional actions necessary)
     *
     * @param Connection $connection
     * @return bool
     */
    public function process(Connection $connection): bool;

    /**
     * @param Connection $connection
     * @return string
     */
    public function getSettingsHtml(Connection $connection): string;
}
