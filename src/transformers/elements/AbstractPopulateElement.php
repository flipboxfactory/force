<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers\elements;

use craft\base\ElementInterface;
use flipbox\force\fields\Objects;
use Flipbox\Transform\Transformers\AbstractTransformer;

abstract class AbstractPopulateElement extends AbstractTransformer
{
    use PopulateAssociationTrait;

    /**
     * @param ElementInterface $element
     * @param array $data
     * @return void
     */
    abstract protected function populateElement(ElementInterface $element, array $data);

    /**
     * @param array $data
     * @param ElementInterface $element
     * @param Objects $field
     * @return ElementInterface
     * @throws \Throwable
     */
    public function __invoke(
        array $data,
        ElementInterface $element,
        Objects $field
    ): ElementInterface {
        $this->populate($element, $data, $field);
        return $element;
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
     * @param Objects $field
     * @return string|null
     */
    protected function getObjectId(array $data, Objects $field)
    {
        $id = $data['Id'] ?? ($data['id'] ?? null);

        return $id ? (string)$id : null;
    }
}
