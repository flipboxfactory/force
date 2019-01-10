<?php

/**
 * @noinspection PhpUnusedParameterInspection
 *
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/salesforce/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/salesforce
 */

namespace flipbox\craft\salesforce\transformers;

use craft\base\Element;
use craft\base\ElementInterface;
use flipbox\craft\salesforce\events\CreatePayloadFromElementEvent;
use flipbox\craft\salesforce\fields\Objects;
use flipbox\craft\salesforce\Force;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class CreateUpsertPayloadFromElement extends BaseObject
{
    /**
     * An action used to assemble a unique event name.
     *
     * @var string
     */
    public $action;

    /**
     * @param ElementInterface|Element $element
     * @param Objects $field
     * @param string|null $id
     * @return array
     */
    public function __invoke(
        ElementInterface $element,
        Objects $field,
        string $id = null
    ): array {

        $event = new CreatePayloadFromElementEvent([
            'payload' => $this->createPayload($element, $field, $id)
        ]);

        $name = $event::eventName(
            $field->object,
            $this->action
        );

        Force::info(sprintf(
            "Create payload: Event '%s', Element '%s'",
            $name,
            $element->id . ' - ' . $element->title
        ), __METHOD__);

        $element->trigger($name, $event);

        return $event->getPayload();
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
