<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers\elements;

use craft\base\ElementInterface;
use craft\helpers\Json;
use flipbox\force\Force;
use Flipbox\Transform\Scope;
use Flipbox\Transform\Transformers\AbstractTransformer;

class SObjectId extends AbstractTransformer
{
    /**
     * @inheritdoc
     */
    public function __invoke($data, Scope $scope, string $identifier = null)
    {
        if ($data instanceof ElementInterface) {
            return $this->transformerElementToId($data);
        }

        Force::warning(sprintf(
            "Unable to determine SObject Id because data is not an element: %s",
            Json::encode($data)
        ));

        return $data;
    }

    /**
     * @param ElementInterface $element
     * @return null|string
     */
    protected function transformerElementToId(ElementInterface $element)
    {
        $sObjectId = Force::getInstance()->getSObjectAssociations()->getQuery([
            'select' => ['sObjectId'],
            'elementId' => $element->getId()
        ])->scalar();

        if (!is_string($sObjectId)) {
            Force::warning(sprintf(
                "SObject Id association was not found for element '%s'",
                $element->getId()
            ));

            return null;
        }

        Force::info(sprintf(
            "SObject Id '%s' was found for element '%s'",
            $sObjectId,
            $element->getId()
        ));

        return $sObjectId;
    }
}
