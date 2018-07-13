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
use flipbox\force\fields\Objects;
use flipbox\force\Force;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectId
{
    /**
     * @var Objects
     */
    protected $field;

    /**
     * @param Objects $field
     */
    public function __construct(Objects $field)
    {
        $this->field = $field;
    }

    /**
     * @inheritdoc
     * @param Element $data
     * @return string|null
     */
    public function __invoke(ElementInterface $data)
    {
        $objectId = Force::getInstance()->getObjectAssociations()->getQuery([
            'select' => ['objectId'],
            'elementId' => $data->getId(),
            'siteId' => SiteHelper::ensureSiteId($data->siteId),
            'fieldId' => $this->field->id
        ])->scalar();

        if (!is_string($objectId)) {
            Force::warning(sprintf(
                "Salesforce Object Id association was not found for element '%s'",
                $data->getId()
            ));

            return null;
        }

        Force::info(sprintf(
            "Salesforce Object Id '%s' was found for element '%s'",
            $objectId,
            $data->getId()
        ));

        return $objectId;
    }
}
