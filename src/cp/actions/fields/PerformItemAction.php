<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\actions\fields;

use craft\base\ElementInterface;
use flipbox\ember\actions\traits\Manage;
use flipbox\force\actions\traits\ElementResolverTrait;
use flipbox\force\actions\traits\FieldResolverTrait;
use flipbox\force\fields\actions\ObjectItemActionInterface;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use flipbox\force\records\ObjectAssociation;
use yii\base\Action;
use yii\web\HttpException;

/**
 * Performs an action on an individual field row
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class PerformItemAction extends Action
{
    use ElementResolverTrait,
        FieldResolverTrait,
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
        $record = $this->resolveRecord($field, $element, $id);

        $availableActions = Force::getInstance()->getObjectsField()->getItemActions($field, $element);

        foreach ($availableActions as $availableAction) {
            if ($action === get_class($availableAction)) {
                $action = $availableAction;
                break;
            }
        }

        if (!$action instanceof ObjectItemActionInterface) {
            throw new HttpException(400, 'Field action is not supported by the field');
        }

        return $this->runInternal($action, $field, $element, $record);
    }

    /**
     * @param ObjectItemActionInterface $action
     * @param Objects $field
     * @param ElementInterface $element
     * @param ObjectAssociation $record
     * @return mixed
     * @throws \yii\web\UnauthorizedHttpException
     */
    protected function runInternal(
        ObjectItemActionInterface $action,
        Objects $field,
        ElementInterface $element,
        ObjectAssociation $record
    ) {
        // Check access
        if (($access = $this->checkAccess($action, $field, $element, $record)) !== true) {
            return $access;
        }

        if (!$this->performAction($action, $field, $element, $record)) {
            return $this->handleFailResponse($action);
        }

        return $this->handleSuccessResponse($action);
    }

    /**
     * @param ObjectItemActionInterface $action
     * @param Objects $field
     * @param ElementInterface $element
     * @param ObjectAssociation $record
     * @return bool
     */
    public function performAction(
        ObjectItemActionInterface $action,
        Objects $field,
        ElementInterface $element,
        ObjectAssociation $record
    ): bool {
        return $action->performAction($field, $element, $record);
    }
}