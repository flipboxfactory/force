<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/salesforce/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/salesforce
 */

namespace flipbox\craft\salesforce\transformers;

use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\Json;
use flipbox\craft\salesforce\fields\Objects;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class PopulateElementErrorsFromUpsertResponse
{
    /**
     * @param ResponseInterface $response
     * @param ElementInterface $element
     * @param Objects $field
     * @param string|null $id
     * @return ElementInterface
     */
    public function __invoke(
        ResponseInterface $response,
        ElementInterface $element,
        Objects $field,
        string $id = null
    ): ElementInterface {
        /** @var Element $element */

        $data = Json::decodeIfJson(
            $response->getBody()->getContents()
        );

        $errors = call_user_func_array(
            new InterpretUpsertResponseErrors(),
            [
                $data
            ]
        );

        $element->addErrors($errors);

        return $element;
    }
}
