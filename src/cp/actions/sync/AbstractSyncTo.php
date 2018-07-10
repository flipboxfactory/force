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
     * @param ElementInterface $element
     * @param Objects $field
     * @return mixed
     * @throws \Exception
     */
    protected function runInternal(
        ElementInterface $element,
        Objects $field
    ) {
        // Check access
        if (($access = $this->checkAccess($element, $field)) !== true) {
            return $access;
        }

        if (false === $this->performAction($element, $field)) {
            return $this->handleFailResponse($element);
        }

        return $this->handleSuccessResponse($element);
    }

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @return false|string
     * @throws \yii\base\InvalidConfigException
     */
    protected function performAction(
        ElementInterface $element,
        Objects $field
    ) {
        return Force::getInstance()->getResources()->getObject()->syncUp(
            $element,
            $field
        );
    }

    /**
     * @param ElementInterface $element
     * @return ElementInterface
     */
    protected function handleSuccessResponse(ElementInterface $element)
    {
        // Success status code
        Craft::$app->getResponse()->setStatusCode($element ? 200 : 201);
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
