<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/salesforce/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/salesforce
 */

namespace flipbox\force\transformers\error;

use Flipbox\Skeleton\Error\ErrorInterface;
use Flipbox\Skeleton\Exceptions\InvalidConfigurationException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Populate extends Interpret
{
    /**
     * @var ErrorInterface
     */
    public $object;

    /**
     * @param array $errors
     * @param null $source
     * @return array
     * @throws InvalidConfigurationException
     */
    public function transform(array $errors, $source = null)
    {
        if (null !== ($object = $this->resolveObject($source))) {
            $object->addErrors($errors);
        }

        return parent::transform($errors, $object);
    }

    /**
     * @param null $object
     * @return ErrorInterface|null
     * @throws InvalidConfigurationException
     */
    protected function resolveObject($object = null)
    {
        if ($object !== null && $this->validateObject($object)) {
            return $object;
        }

        $this->validateObject($this->object);

        return $this->object;
    }

    /**
     * @param $object
     * @return bool
     *
     * @throws InvalidConfigurationException
     */
    protected function validateObject($object): bool
    {
        if (!is_object($object)) {
            throw new InvalidConfigurationException(sprintf(
                "The class '%s' requires a valid object.",
                get_class($this)
            ));
        }

        if (!method_exists($object, 'addErrors')) {
            throw new InvalidConfigurationException(sprintf(
                "The class '%s' requires an object which contains an 'addErrors' method., '%s' given.",
                get_class($this),
                get_class($this->object)
            ));
        }

        return true;
    }
}
