<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\collections;

use Flipbox\Skeleton\Collections\Collection as BaseCollection;
use Flipbox\Skeleton\Error\ErrorInterface;
use Flipbox\Skeleton\Error\ErrorTrait;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Collection extends BaseCollection implements ErrorInterface
{
    use ErrorTrait;

    /**
     * Errors may be transformed and passed in the constructor config
     *
     * @param array $errors
     * @return $this
     */
    public function setErrors(array $errors)
    {
        foreach ($errors as $key => $error) {
            $this->addError($key, $error);
        }
        return $this;
    }
}
