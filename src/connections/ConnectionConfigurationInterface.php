<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\connections;

use flipbox\force\records\Connection;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface ConnectionConfigurationInterface
{
    /**
     * ConnectionTypeInterface constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection);

    /**
     * @return string
     */
    public static function displayName(): string;

    /**
     * Process / Save a connection (and preform any additional actions necessary)
     *
     * @return bool
     */
    public function process(): bool;

    /**
     * @return string
     */
    public function getSettingsHtml(): string;
}
