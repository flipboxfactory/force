<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\actions\fields;

use craft\base\ElementInterface;
use flipbox\ember\actions\traits\Manage;
use flipbox\force\criteria\SObjectCriteria;
use flipbox\force\fields\actions\SObjectRowActionInterface;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use yii\base\Action;
use yii\web\HttpException;

/**
 * Performs an action on an individual field row
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class PerformRowAction extends Action
{
    use traits\ElementResolverTrait,
        traits\FieldResolverTrait,
        Manage;

    /**
     * @param string $field
     * @param string $element
     * @param string|null $action
     * @param string|null $id
     * @return mixed
     * @throws HttpException
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\InvalidConfigException
     */
    public function run(string $field, string $element, string $action, string $id)
    {
        $field = $this->resolveField($field);
        $element = $this->resolveElement($element);
        $criteria = $this->resolveCriteria($field, $element, $id);

        $availableActions = Force::getInstance()->getObjectsField()->getRowActions($field, $element);

        foreach ($availableActions as $availableAction) {
            if ($action === get_class($availableAction)) {
                $action = $availableAction;
                break;
            }
        }

        if (!$action instanceof SObjectRowActionInterface) {
            throw new HttpException(400, 'Field action is not supported by the field');
        }

        return $this->runInternal($action, $field, $element, $criteria);
    }

    /**
     * @param SObjectRowActionInterface $action
     * @param Objects $field
     * @param ElementInterface $element
     * @param SObjectCriteria $criteria
     * @return mixed
     * @throws \yii\web\UnauthorizedHttpException
     */
    protected function runInternal(
        SObjectRowActionInterface $action,
        Objects $field,
        ElementInterface $element,
        SObjectCriteria $criteria
    ) {
        // Check access
        if (($access = $this->checkAccess($action, $field, $element, $criteria)) !== true) {
            return $access;
        }

        if (!$this->performAction($action, $field, $element, $criteria)) {
            return $this->handleFailResponse($action);
        }

        return $this->handleSuccessResponse($action);
    }

    /**
     * @param SObjectRowActionInterface $action
     * @param Objects $field
     * @param ElementInterface $element
     * @param SObjectCriteria $criteria
     * @return bool
     */
    public function performAction(
        SObjectRowActionInterface $action,
        Objects $field,
        ElementInterface $element,
        SObjectCriteria $criteria
    ): bool {
        return $action->performAction($field, $element, $criteria);
    }
}
