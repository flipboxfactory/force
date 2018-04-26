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
use yii\base\InvalidArgumentException;

class SObjectPayload extends AbstractTransformer
{
    /**
     * @param $data
     * @param Scope $scope
     * @param string|null $identifier
     * @param string|null $sObject
     * @return mixed
     */
    public function __invoke(
        $data,
        Scope $scope,
        string $identifier = null,
        string $sObject = null
    ) {
        if (empty($sObject)) {
            throw new InvalidArgumentException("Salesforce Object must be defined.");
        }

        if ($data instanceof ElementInterface) {
            return $this->transformElement($data, $sObject);
        }

        return $data;
    }

    /**
     * @param ElementInterface $element
     * @param string $sObject
     * @return array|null
     */
    protected function transformElement(ElementInterface $element, string $sObject)
    {
        $transformer = Force::getInstance()->getTransformers()->find(
            TransformerHelper::eventName(['sobject', $sObject, 'payload']),
            get_class($element)
        );

        if ($transformer !== null) {
            return Factory::item(
                $transformer,
                $element,
                [],
                [
                    'data' => $element,
                    'sObject' => $sObject
                ]
            );
        }

        return null;
    }
}
