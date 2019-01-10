<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\criteria;

use Flipbox\Salesforce\Connections\ConnectionInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface ObjectMutatorCriteriaInterface
{
    /**
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface;

    /**
     * @return CacheInterface
     */
    public function getCache(): CacheInterface;

    /**
     * @return string
     */
    public function getObject();

    /**
     * @return string
     */
    public function getId();

    /**
     * @return array
     */
    public function getPayload(): array;
}
