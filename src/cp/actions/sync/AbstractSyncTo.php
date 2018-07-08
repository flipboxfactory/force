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
use yii\base\Action;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class AbstractSyncTo extends Action
{
    use CheckAccess;

    /**
     * @param SObjectCriteria $value
     * @param ElementInterface $element
     * @param Objects $field
     * @return mixed
     * @throws \Exception
     */
    protected function runInternal(
        SObjectCriteria $value,
        ElementInterface $element,
        Objects $field
    ) {
        // Check access
        if (($access = $this->checkAccess($value, $element, $field)) !== true) {
            return $access;
        }

        if (false === $this->performAction($value, $element, $field)) {
            return $this->handleFailResponse();
        }

        return $this->handleSuccessResponse($value);
    }

    /**
     * @param SObjectCriteria $value
     * @param ElementInterface $element
     * @param Objects $field
     * @return false|string
     * @throws \yii\base\InvalidConfigException
     */
    protected function performAction(
        SObjectCriteria $value,
        ElementInterface $element,
        Objects $field
    ) {
        return Force::getInstance()->getElements()->syncUp(
            $element,
            $field,
            $value
        );
    }

    /**
     * @param SObjectCriteria $criteria
     * @return array
     */
    protected function handleSuccessResponse(SObjectCriteria $criteria)
    {
        // Success status code
        Craft::$app->getResponse()->setStatusCode(empty($criteria->id) ? 200 : 201);
        return ['sObjectId' => $criteria->id];
    }

    /**
     * @return mixed
     */
    protected function handleFailResponse()
    {
        Craft::$app->getResponse()->setStatusCode(400);
        return;
    }
}
