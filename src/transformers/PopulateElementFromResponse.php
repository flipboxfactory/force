<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/salesforce/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/salesforce
 */

namespace flipbox\force\transformers;

use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\Json;
use flipbox\craft\ember\helpers\SiteHelper;
use flipbox\craft\integration\queries\IntegrationConnectionQuery;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use flipbox\force\records\ObjectAssociation;
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
    ): ElementInterface {
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
    ) {
        $this->populateElementObjectIdFromResponse($response, $element, $field, $id);
    }

    /**
     * @param ResponseInterface $response
     * @param ElementInterface|Element $element
     * @param Objects $field
     * @param string|null $id
     */
    protected function populateElementObjectIdFromResponse(
        ResponseInterface $response,
        ElementInterface $element,
        Objects $field,
        string $id = null
    ) {
        // Not already associated?
        if($id === null) {

            if (null === ($objectId = $this->getObjectIdFromResponse($response))) {
                Force::error("Unable to determine object id from response");
                return;
            };


            /** @var IntegrationConnectionQuery $query */
            $query = $element->getFieldValue($field->handle);
            $query->indexBy = ['objectId'];

            $associations = $query->all();

            if (!array_key_exists($objectId, $associations)) {
                $recordClass = $field::recordClass();

                /** @var ObjectAssociation $association */
                $association = new $recordClass;

                $association->setField($field)
                    ->setElement($element)
                    ->setSiteId(SiteHelper::ensureSiteId($element->siteId));
                $association->objectId = $objectId;

                $associations[$objectId] = $associations;

                $query->setCachedResult(array_values($associations));
            }
        }
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
