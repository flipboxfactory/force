<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers\elements;

use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\Json;
use flipbox\ember\helpers\SiteHelper;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use Flipbox\Transform\Transformers\AbstractSimpleTransformer;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectId extends AbstractSimpleTransformer
{
    /**
     * @var Objects
     */
    protected $field;

    /**
     * @param Objects $field
     * @inheritdoc
     */
    public function __construct(Objects $field, $config = [])
    {
        $this->field = $field;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     * @return string|null
     */
    public function __invoke($data, string $identifier = null)
    {
        if ($data instanceof ElementInterface) {
            return $this->transformerElementToId($data);
        }

        Force::warning(sprintf(
            "Unable to determine Force Id because data is not an element: %s",
            Json::encode($data)
        ));

        return null;
    }

    /**
     * @param Element|ElementInterface $element
     * @return null|string
     */
    protected function transformerElementToId(ElementInterface $element)
    {
        $objectId = Force::getInstance()->getObjectAssociations()->getQuery([
            'select' => ['objectId'],
            'elementId' => $element->getId(),
            'siteId' => SiteHelper::ensureSiteId($element->siteId),
            'fieldId' => $this->field->id
        ])->scalar();

        if (!is_string($objectId)) {
            Force::warning(sprintf(
                "Force Object Id association was not found for element '%s'",
                $element->getId()
            ));

            return null;
        }

        Force::info(sprintf(
            "Force Object Id '%s' was found for element '%s'",
            $objectId,
            $element->getId()
        ));

        return $objectId;
    }
}
