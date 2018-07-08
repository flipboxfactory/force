<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\connections;

use Flipbox\Relay\Salesforce\AuthorizationInterface;
use Flipbox\Relay\Salesforce\InstanceInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface ConnectionInterface extends InstanceInterface, AuthorizationInterface
{
    /**
     * The Salesforce Connection Instance URL
     *
     * @return string
     */
    public function getInstanceUrl(): string;
}
