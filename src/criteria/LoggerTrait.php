<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\criteria;

use Psr\Log\LoggerInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait LoggerTrait
{
    /**
     * @var LoggerInterface|string|null
     */
    protected $logger;

    /**
     * @param $value
     * @return $this
     */
    public function logger($value)
    {
        return $this->setLogger($value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setLogger($value)
    {
        $this->logger = $value;
        return $this;
    }

    /**
     * @return LoggerInterface|null
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
