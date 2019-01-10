<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/salesforce/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/salesforce
 */

namespace flipbox\craft\salesforce\transformers;

use craft\base\Element;
use craft\base\ElementInterface;
use flipbox\craft\salesforce\events\PopulateElementFromResponseEvent;
use flipbox\craft\salesforce\fields\Objects;
use flipbox\craft\salesforce\Force;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class PopulateElementFromResponse
{
    /**
     * An action used to assemble a unique event name.
     *
     * @var string
     */
    public $action;

    /**
     * @param ResponseInterface $response
     * @param ElementInterface $element
     * @param Objects $field
     * @param string $objectId
     * @return ElementInterface
     */
    public function __invoke(
        ResponseInterface $response,
        ElementInterface $element,
        Objects $field,
        string $objectId
    ): ElementInterface {
        $this->populateElementFromResponse($response, $element, $field, $objectId);
        return $element;
    }

    /**
     * @param ResponseInterface $response
     * @param ElementInterface|Element $element
     * @param Objects $field
     * @param string $objectId
     * @return ElementInterface
     */
    protected function populateElementFromResponse(
        ResponseInterface $response,
        ElementInterface $element,
        Objects $field,
        string $objectId
    ): ElementInterface {

        $event = new PopulateElementFromResponseEvent([
            'response' => $response,
            'field' => $field,
            'objectId' => $objectId
        ]);

        $name = $event::eventName(
            $field->object,
            $this->action
        );

        Force::info(sprintf(
            "Populate Element: Event '%s', Element '%s'",
            $name,
            $element->id . ' - ' . $element->title
        ), __METHOD__);

        $element->trigger($name, $event);

        return $event->sender ?: $element;
    }
}
