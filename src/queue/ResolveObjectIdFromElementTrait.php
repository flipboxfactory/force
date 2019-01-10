<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\queue;

use craft\base\Element;
use craft\base\ElementInterface;
use flipbox\craft\ember\helpers\SiteHelper;
use flipbox\craft\integration\records\IntegrationAssociation;
use flipbox\craft\salesforce\fields\Objects;
use flipbox\craft\salesforce\Force;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait ResolveObjectIdFromElementTrait
{
    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @return null|string
     */
    protected function resolveObjectIdFromElement(
        ElementInterface $element,
        Objects $field
    ) {
        /** @var Element $element */

        /** @var IntegrationAssociation $recordClass */
        $recordClass = $field::recordClass();

        $query = $recordClass::find();

        $query->elementId($element->getId())
            ->fieldId($field->id)
            ->siteId(SiteHelper::ensureSiteId($element->siteId));

        $query->select(['objectId']);

        $objectId = $query->scalar();

        if (!is_string($objectId)) {
            Force::warning(sprintf(
                "Salesforce Object Id association was not found for element '%s'",
                $element->getId()
            ));

            return null;
        }

        Force::info(sprintf(
            "Salesforce Object Id '%s' was found for element '%s'",
            $objectId,
            $element->getId()
        ));

        return $objectId;
    }
}
