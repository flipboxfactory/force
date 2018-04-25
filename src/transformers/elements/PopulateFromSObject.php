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
use Flipbox\Transform\Factory;
use Flipbox\Transform\Scope;
use Flipbox\Transform\Transformers\AbstractTransformer;

class PopulateFromSObject extends AbstractTransformer
{
    /**
     * @param $data
     * @param Scope $scope
     * @param string|null $identifier
     * @param ElementInterface|null $source
     * @param string|null $sObject
     * @return mixed
     */
    public function __invoke(
        $data,
        Scope $scope,
        string $identifier = null,
        ElementInterface $source = null,
        string $sObject = null
    ) {
        // This is common when preforming an update/delete
        if (empty($data)) {
            Force::info(
                "Data is empty; therefore there is nothing to populate.",
                __METHOD__
            );

            return $data;
        }

        if (!$source instanceof ElementInterface) {
            Force::warning(
                "Unable to populate element because an element 'source' does not exist.",
                __METHOD__
            );

            return $data;
        }

        $sObject = $data['attributes']['type'] ?? $sObject;
        if ($sObject === null) {
            Force::warning(
                "Unable to populate element because the SObject type could not be determined.",
                __METHOD__
            );

            return $data;
        }

        $this->transformElement($data, $source, $sObject);

        return $data;
    }

    /**
     * @param array $data
     * @param ElementInterface $element
     * @param string $sObject
     */
    protected function transformElement(array $data, ElementInterface $element, string $sObject)
    {
        $event = TransformerHelper::eventName(['sobject', $sObject, 'populate']);
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
            ['source' => $element]
        );
    }
}
