<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\criteria;

use Flipbox\Salesforce\Connections\ConnectionInterface;
use Flipbox\Salesforce\Transformers\Collections\TransformerCollectionInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface CriteriaInterface
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
     * @return TransformerCollectionInterface|null
     */
    public function getTransformer();
}
