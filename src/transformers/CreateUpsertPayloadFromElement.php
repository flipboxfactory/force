<?php

/**
 * @noinspection PhpUnusedParameterInspection
 *
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/salesforce/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/salesforce
 */

namespace flipbox\force\transformers;

use craft\base\Element;
use craft\base\ElementInterface;
use flipbox\force\fields\Objects;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class CreateUpsertPayloadFromElement
{
    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param string|null $id
     * @return array
     */
    public function __invoke(
        ElementInterface $element,
        Objects $field,
        string $id = null
    ): array {
        return $this->createPayload($element, $field, $id);
    }

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param string|null $id
     * @return array
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function createPayload(
        ElementInterface $element,
        Objects $field,
        string $id = null
    ): array {
        /** @var Element $element */

        return [];
    }
}
