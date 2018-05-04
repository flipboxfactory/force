<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers;

use craft\base\ElementInterface;
use flipbox\flux\helpers\TransformerHelper;
use flipbox\force\Force;
use Flipbox\Transform\Factory;
use Flipbox\Transform\Scope;
use Flipbox\Transform\Transformers\AbstractTransformer;
use yii\base\DynamicModel;

class DynamicModelSuccess extends AbstractTransformer
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
        if (!is_array($data)) {
            $data = [$data];
        }

        if (!$source instanceof ElementInterface) {
            Force::warning(
                "Unable to populate element because an element 'source' does not exist.",
                __METHOD__
            );

            return $this->transform($data);
        }

        $this->populateSource($source, $data, $sObject);

        return $this->transform($data);
    }

    /**
     * @param ElementInterface $element
     * @param array $data
     * @param string|null $sObject
     */
    protected function populateSource(ElementInterface $element, array $data, string $sObject = null)
    {
        $event = ['populate'];

        if (null !== ($sObject = $data['attributes']['type'] ?? $sObject)) {
            $event = ['sobject', $sObject] + $event;
        }

        $event = TransformerHelper::eventName($event);
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

        Force::info(
            sprintf(
                "Populate element '%s' with transformer event '%s'",
                $class,
                $event
            ),
            __METHOD__
        );

        Factory::item(
            $transformer,
            $data,
            [],
            ['source' => $element, 'sObject' => $sObject]
        );
    }

    /**
     * @param array $data
     * @return DynamicModel
     */
    protected function transform(array $data): DynamicModel
    {
        return new DynamicModel(array_keys($data), $data);
    }
}
