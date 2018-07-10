<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers\elements;

use craft\base\ElementInterface;
use flipbox\flux\helpers\TransformerHelper;
use flipbox\force\Force;
use flipbox\force\transformers\ResponseToDynamicModel;
use Flipbox\Transform\Factory;
use Flipbox\Transform\Scope;

class PopulateFromSObject extends ResponseToDynamicModel
{
    /**
     * @param $data
     * @param Scope $scope
     * @param string|null $identifier
     * @param ElementInterface|null $source
     * @param string|null $object
     * @return mixed
     */
    public function __invoke(
        $data,
        Scope $scope,
        string $identifier = null,
        ElementInterface $source = null,
        string $object = null
    ) {
        if ($source instanceof ElementInterface) {
            if (null === ($object = $data['attributes']['type'] ?? $object)) {
                Force::warning(
                    "Unable to populate element because the SObject type could not be determined.",
                    __METHOD__
                );

                return $this->transform($data);
            }

            $this->populateElement($data, $source, $object);

            return $this->transform($data);
        }

        Force::warning(
            "Unable to populate element because an element 'source' does not exist.",
            __METHOD__
        );

        return $this->transform($data);
    }

    /**
     * @param array $data
     * @param ElementInterface $element
     * @param string $object
     */
    protected function populateElement(array $data, ElementInterface $element, string $object)
    {
        $event = TransformerHelper::eventName(['object', $object, 'populate']);
        $class = get_class($element);

        if (null === ($transformer = Force::getInstance()->getTransformers()->find($event, $class))) {
            Force::warning(
                sprintf(
                    "Populate element '%s' transformer could not be found for event '%s'",
                    $class,
                    $event
                ),
                __METHOD__
            );

            return;
        }

        Factory::item(
            $transformer,
            $data,
            [],
            ['source' => $element, 'object' => $object]
        );
    }
}
