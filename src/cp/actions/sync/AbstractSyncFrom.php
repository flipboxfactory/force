<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\actions\sync;

use Craft;
use craft\base\ElementInterface;
use flipbox\ember\actions\traits\CheckAccess;
use flipbox\force\criteria\SObjectCriteria;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use flipbox\force\transformers\collections\TransformerCollection;
use yii\base\Action;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class AbstractSyncFrom extends Action
{
    use CheckAccess;

    /**
     * @param SObjectCriteria $criteria
     * @param ElementInterface $element
     * @param Objects $field
     * @return mixed
     * @throws \Exception
     */
    protected function runInternal(
        SObjectCriteria $criteria,
        ElementInterface $element,
        Objects $field
    ) {
        // Check access
        if (($access = $this->checkAccess($criteria, $element, $field)) !== true) {
            return $access;
        }

        if (false === $this->performAction($criteria, $element, $field)) {
            return $this->handleFailResponse($element);
        }

        return $this->handleSuccessResponse($element);
    }

    /**
     * @param SObjectCriteria $criteria
     * @param ElementInterface $element
     * @param Objects $field
     * @return false|string
     * @throws \yii\base\InvalidConfigException
     */
    protected function performAction(
        SObjectCriteria $criteria,
        ElementInterface $element,
        Objects $field
    ) {
        $criteria->transformer([
            'class' => TransformerCollection::class
        ]);

        return Force::getInstance()->getElements()->syncDown(
            $element,
            $field,
            $criteria
        );
    }

    /**
     * @param ElementInterface $element
     * @return ElementInterface
     */
    protected function handleSuccessResponse(ElementInterface $element)
    {
        Craft::$app->getResponse()->setStatusCode(200);
        return $element;
    }

    /**
     * @param ElementInterface $element
     * @return ElementInterface
     */
    protected function handleFailResponse(ElementInterface $element)
    {
        Craft::$app->getResponse()->setStatusCode(400);
        return $element;
    }
}
