<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\traits;

use craft\base\ElementInterface;
use flipbox\force\fields\Objects;
use flipbox\force\transformers\elements\ObjectId;
use Flipbox\Transform\Factory;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait TransformElementIdTrait
{
    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @return null|string
     */
    protected function transformElementId(
        ElementInterface $element,
        Objects $field
    ) {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        $id = Factory::item(
            new ObjectId($field),
            $element,
            [
                'element' => $element
            ]
        );

        return !empty($id) ? (string)$id : null;
    }
}
