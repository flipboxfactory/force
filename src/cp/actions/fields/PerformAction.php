<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\actions\fields;

use Craft;
use craft\base\ElementInterface;
use flipbox\ember\actions\traits\Manage;
use flipbox\force\fields\actions\SObjectActionInterface;
use flipbox\force\fields\SObjects;
use flipbox\force\Force;
use yii\base\Action;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class PerformAction extends Action
{
    use traits\ElementResolverTrait,
        traits\FieldResolverTrait,
        Manage;

    /**
     * @param string $field
     * @param string $element
     * @param string|null $action
     * @return mixed
     * @throws HttpException
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\InvalidConfigException
     */
    public function run(string $field, string $element, string $action = null)
    {
        $field = $this->resolveField($field);
        $element = $this->resolveElement($element);

        $availableActions = Force::getInstance()->getSObjectsField()->getActions($field);

        foreach ($availableActions as $availableAction) {
            if ($action === get_class($availableAction)) {
                $action = $availableAction;
                break;
            }
        }

        if (!$action instanceof SObjectActionInterface) {
            throw new HttpException(400, 'Field action is not supported by the field');
        }

        return $this->runInternal($action, $field, $element);
    }

    /**
     * @param SObjectActionInterface $action
     * @param SObjects $field
     * @param ElementInterface $element
     * @return mixed
     * @throws \yii\web\UnauthorizedHttpException
     */
    protected function runInternal(
        SObjectActionInterface $action,
        SObjects $field,
        ElementInterface $element
    ) {
        // Check access
        if (($access = $this->checkAccess($action, $field, $element)) !== true) {
            return $access;
        }

        if (!$this->performAction($action, $field, $element)) {
            return $this->handleFailResponse($action);
        }

        return $this->handleSuccessResponse($action);
    }

    /**
     * @param SObjectActionInterface $action
     * @param SObjects $field
     * @param ElementInterface $element
     * @return bool
     */
    public function performAction(
        SObjectActionInterface $action,
        SObjects $field,
        ElementInterface $element
    ): bool {
        return $action->performAction($field, $element);
    }
}
