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
use Flipbox\Transform\Scope;
use Flipbox\Transform\Transformers\AbstractTransformer;
use yii\base\InvalidArgumentException;

abstract class AbstractPopulateFromObject extends AbstractTransformer
{
    /**
     * @param ElementInterface $element
     * @param array $data
     * @return void
     */
    abstract protected function populateElement(ElementInterface $element, array $data);

    /**
     * @inheritdoc
     * @throws \Throwable
     */
    public function __invoke(
        $data,
        Scope $scope,
        string $identifier = null,
        ElementInterface $source = null,
        Objects $field = null
    ) {
        if ($field === null) {
            throw new InvalidArgumentException(
                sprintf(
                    "Field must be an instance of '%s'",
                    Objects::class
                )
            );
        }

        if ($source === null) {
            throw new InvalidArgumentException(
                sprintf(
                    "Source must be an instance of '%s'",
                    ElementInterface::class
                )
            );
        }

        $this->populate($source, $data, $field);

        return $data;
    }

    /**
     * @param array $data
     * @param ElementInterface $element
     * @param Objects $field
     * @throws \Throwable
     */
    protected function populate(ElementInterface $element, array $data, Objects $field)
    {
        $this->populateElement($element, $data);
        $this->populateAssociation($element, $data, $field);
    }

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

    /**
     * @param array $data
     * @param Objects $field
     * @return string|null
     */
    protected function getObjectId(array $data, Objects $field)
    {
        $id = $data['Id'] ?? ($data['id'] ?? null);

        return $id ? (string)$id : null;
    }
}
