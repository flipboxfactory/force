<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\actions\fields;

use craft\base\ElementInterface;
use flipbox\ember\actions\traits\Manage;
use flipbox\force\criteria\ObjectMutatorCriteriaInterface;
use flipbox\force\fields\actions\ObjectItemActionInterface;
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

        if (!$action instanceof ObjectItemActionInterface) {
            throw new HttpException(400, 'Field action is not supported by the field');
        }

        return $this->runInternal($action, $field, $element, $criteria);
    }

    /**
     * @param ObjectItemActionInterface $action
     * @param Objects $field
     * @param ElementInterface $element
     * @param ObjectMutatorCriteriaInterface $criteria
     * @return mixed
     * @throws \yii\web\UnauthorizedHttpException
     */
    protected function runInternal(
        ObjectItemActionInterface $action,
        Objects $field,
        ElementInterface $element,
        ObjectMutatorCriteriaInterface $criteria
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
     * @param ObjectItemActionInterface $action
     * @param Objects $field
     * @param ElementInterface $element
     * @param ObjectMutatorCriteriaInterface $criteria
     * @return bool
     */
    public function performAction(
        ObjectItemActionInterface $action,
        Objects $field,
        ElementInterface $element,
        ObjectMutatorCriteriaInterface $criteria
    ): bool {
        return $action->performAction($field, $element, $criteria);
    }
}
