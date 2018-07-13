<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers\elements;

use craft\base\Element;
use craft\base\ElementInterface;
use flipbox\ember\helpers\SiteHelper;
use flipbox\force\db\ObjectAssociationQuery;
use flipbox\force\fields\Objects;
use flipbox\force\records\ObjectAssociation;

trait PopulateAssociationTrait
{
    /**
     * @param array $data
     * @param Objects $field
     * @return string|null
     */
    protected abstract function getObjectId(array $data, Objects $field);

    /**
     * @param array $data
     * @param ElementInterface $element
     * @param Objects $field
     * @throws \Throwable
     */
    protected function populateAssociation(ElementInterface $element, array $data, Objects $field)
    {
        $objectId = $this->getObjectId($data, $field);

        /** @var ObjectAssociationQuery $fieldValue */
        if (null === ($fieldValue = $element->{$field->handle})) {
            return;
        };

        /** @var Element $element */

        $associations = $fieldValue->indexBy('objectId')->all();

        if (!array_key_exists($objectId, $associations)) {
            $associations[$objectId] = new ObjectAssociation([
                'field' => $field,
                'element' => $element,
                'objectId' => $objectId,
                'siteId' => SiteHelper::ensureSiteId($element->siteId)
            ]);

            $fieldValue->setCachedResult(array_values($associations));
        }
    }
}
