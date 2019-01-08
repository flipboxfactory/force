<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/salesforce/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/salesforce
 */

namespace flipbox\force\transformers;

use craft\base\ElementInterface;
use craft\helpers\Json;
use flipbox\force\fields\Objects;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class PopulateElementFromResponse
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
    ): ElementInterface
    {
        $this->populateElementFromResponse($response, $element, $field, $id);
        return $element;
    }

    /**
     * @param ResponseInterface $response
     * @param ElementInterface $element
     * @param Objects $field
     * @param string|null $id
     */
    protected function populateElementFromResponse(
        ResponseInterface $response,
        ElementInterface $element,
        Objects $field,
        string $id = null
    )
    {
        $this->populateElementObjectIdFromResponse($response, $element, $field, $id);
    }

    /**
     * @param ResponseInterface $response
     * @param ElementInterface $element
     * @param Objects $field
     * @param string|null $id
     */
    protected function populateElementObjectIdFromResponse(
        ResponseInterface $response,
        ElementInterface $element,
        Objects $field,
        string $id = null
    )
    {
        $element->{$field->handle} = $id ?: $this->getObjectIdFromResponse($response);
    }

    /**
     * @param ResponseInterface $response
     * @return string|null
     */
    protected function getObjectIdFromResponse(ResponseInterface $response)
    {
        $data = Json::decodeIfJson(
            $response->getBody()->getContents()
        );

        $id = $data['Id'] ?? ($data['id'] ?? null);

        return $id ? (string)$id : null;
    }
}