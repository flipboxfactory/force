<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\traits;

use craft\base\ElementInterface;
use flipbox\flux\helpers\TransformerHelper;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use Flipbox\Transform\Factory;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait TransformElementPayloadTrait
{
    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @return array
     */
    protected function transformElementPayload(
        ElementInterface $element,
        Objects $field
    ): array {

        $transformer = Force::getInstance()->getTransformers()->find(
            TransformerHelper::eventName([$field->sObject, 'payload']),
            get_class($element)
        );

        if ($transformer !== null) {
            return (array)Factory::item(
                $transformer,
                $element
            );
        }

        return [];
    }
}
