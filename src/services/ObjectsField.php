<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\base\FieldInterface;
use craft\helpers\Component as ComponentHelper;
use craft\helpers\StringHelper;
use flipbox\craft\sortable\associations\db\SortableAssociationQueryInterface;
use flipbox\craft\sortable\associations\records\SortableAssociationInterface;
use flipbox\craft\sortable\associations\services\SortableFields;
use flipbox\force\criteria\SObjectCriteria;
use flipbox\force\db\SObjectFieldQuery;
use flipbox\force\events\RegisterSObjectFieldActionsEvent;
use flipbox\force\fields\actions\SObjectActionInterface;
use flipbox\force\fields\actions\SObjectRowActionInterface;
use flipbox\force\fields\actions\SyncRowFrom;
use flipbox\force\fields\actions\SyncRowTo;
use flipbox\force\fields\actions\SyncTo;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use flipbox\force\records\SObjectAssociation;
use flipbox\force\web\assets\sobjects\SObjects as SObjectsAsset;
use yii\base\Exception;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectsField extends SortableFields
{
    /**
     * @inheritdoc
     */
    const SOURCE_ATTRIBUTE = SObjectAssociation::SOURCE_ATTRIBUTE;

    /**
     * @inheritdoc
     */
    const TARGET_ATTRIBUTE = SObjectAssociation::TARGET_ATTRIBUTE;

    /**
     * @var Objects[]
     */
    private $fields = [];

    /**
     * @inheritdoc
     */
    protected static function tableAlias(): string
    {
        return SObjectAssociation::tableAlias();
    }

    /**
     * @param int $id
     * @return Objects|null
     */
    public function findById(int $id)
    {
        if (!array_key_exists($id, $this->fields)) {
            $sObjectField = Craft::$app->getFields()->getFieldById($id);
            if (!$sObjectField instanceof Objects) {
                $sObjectField = null;
            }

            $this->fields[$id] = $sObjectField;
        }

        return $this->fields[$id];
    }


    /**
     * @param FieldInterface $field
     * @param ElementInterface|null $element
     * @return SortableAssociationQueryInterface
     * @throws Exception
     */
    public function getQuery(
        FieldInterface $field,
        ElementInterface $element = null
    ): SortableAssociationQueryInterface {
        /** @var Objects $field */
        $this->ensureField($field);

        $query = new SObjectFieldQuery($field);

        if ($field->max !== null) {
            $query->limit($field->max);
        }

        $query->siteId = $this->targetSiteId($element);
        $query->element = $element === null ? null : $element->getId();

        return $query;
    }


    /*******************************************
     * NORMALIZE VALUE
     *******************************************/

    /**
     * @inheritdoc
     */
    protected function normalizeQueryInputValue(
        FieldInterface $field,
        $value,
        int &$sortOrder,
        ElementInterface $element = null
    ): SortableAssociationInterface {
        /** @var Objects $field */
        $this->ensureField($field);

        if (is_array($value)) {
            $value = StringHelper::toString($value);
        }

        return Force::getInstance()->getObjectAssociations()->create(
            [
                'fieldId' => $field->id,
                SObjectAssociation::TARGET_ATTRIBUTE => $value,
                SObjectAssociation::SOURCE_ATTRIBUTE => $element === null ? null : $element->getId(),
                'siteId' => $this->targetSiteId($element),
                'sortOrder' => $sortOrder++
            ]
        );
    }

    /**
     * @param Objects $field
     * @param SObjectFieldQuery $query
     * @param ElementInterface|null $element
     * @param bool $static
     * @return null|string
     * @throws Exception
     * @throws \Twig_Error_Loader
     */
    public function getInputHtml(
        Objects $field,
        SObjectFieldQuery $query,
        ElementInterface $element = null,
        bool $static
    ) {
        Craft::$app->getView()->registerAssetBundle(SObjectsAsset::class);

        return Craft::$app->getView()->renderTemplate(
            'force/_components/fieldtypes/SObjects/input',
            [
                'field' => $field,
                'element' => $element,
                'value' => $query,
                'actions' => $this->getActionHtml($field, $element),
                'rowActions' => $this->getRowActionHtml($field, $element),
                'static' => $static
            ]
        );
    }

    /**
     * @param Objects $field
     * @return null|string
     * @throws Exception
     * @throws \Twig_Error_Loader
     */
    public function getSettingsHtml(
        Objects $field
    ) {
        return Craft::$app->getView()->renderTemplate(
            'force/_components/fieldtypes/SObjects/settings',
            [
                'field' => $field
            ]
        );
    }

    /**
     * @param FieldInterface $field
     * @throws Exception
     */
    private function ensureField(FieldInterface $field)
    {
        if (!$field instanceof Objects) {
            throw new Exception(sprintf(
                "The field must be an instance of '%s', '%s' given.",
                (string)Objects::class,
                (string)get_class($field)
            ));
        }
    }

    /**
     * @param Objects $field
     * @param SObjectCriteria $value
     * @param ElementInterface $element
     * @return bool
     */
    public function saveAssociation(
        Objects $field,
        SObjectCriteria $value,
        ElementInterface $element
    ) {
        /** @var Element $element */
        $association = Force::getInstance()->getObjectAssociations()->create([
            'fieldId' => $field->id,
            'siteId' => $element->siteId,
            'sObjectId' => $value->id,
            'elementId' => $element->getId()
        ]);

        return $association->associate();
    }

    /**
     * @param Objects $field
     * @param ElementInterface|null $element
     * @return array
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\InvalidConfigException
     */
    protected function getActionHtml(Objects $field, ElementInterface $element = null): array
    {
        $actionData = [];

        foreach ($this->getActions($field, $element) as $action) {
            $actionData[] = [
                'type' => get_class($action),
                'destructive' => $action->isDestructive(),
                'params' => [],
                'name' => $action->getTriggerLabel(),
                'trigger' => $action->getTriggerHtml(),
                'confirm' => $action->getConfirmationMessage(),
            ];
        }

        return $actionData;
    }

    /**
     * @param Objects $field
     * @param ElementInterface|null $element
     * @return array
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\InvalidConfigException
     */
    protected function getRowActionHtml(Objects $field, ElementInterface $element = null): array
    {
        $actionData = [];

        foreach ($this->getRowActions($field, $element) as $action) {
            $actionData[] = [
                'type' => get_class($action),
                'destructive' => $action->isDestructive(),
                'params' => [],
                'name' => $action->getTriggerLabel(),
                'trigger' => $action->getTriggerHtml(),
                'confirm' => $action->getConfirmationMessage(),
            ];
        }

        return $actionData;
    }

    /**
     * @param Objects $field
     * @param ElementInterface|null $element
     * @return SObjectRowActionInterface[]
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\InvalidConfigException
     */
    public function getRowActions(Objects $field, ElementInterface $element = null): array
    {
        $event = new RegisterSObjectFieldActionsEvent([
            'actions' => [
                SyncRowFrom::class,
                SyncRowTo::class
            ],
            'element' => $element
        ]);

        $field->trigger(
            $field::EVENT_REGISTER_ROW_ACTIONS,
            $event
        );

        return $this->resolveActions($event->actions, SObjectRowActionInterface::class);
    }

    /**
     * @param Objects $field
     * @param ElementInterface|null $element
     * @return SObjectActionInterface[]
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\InvalidConfigException
     */
    public function getActions(Objects $field, ElementInterface $element = null): array
    {
        $actions = [];

        if (!empty($field->id)) {
            $actions[] = SyncTo::class;
        }

        $event = new RegisterSObjectFieldActionsEvent([
            'actions' => $actions,
            'element' => $element
        ]);

        $field->trigger(
            $field::EVENT_REGISTER_ACTIONS,
            $event
        );

        return $this->resolveActions($event->actions, SObjectActionInterface::class);
    }

    /**
     * @param array $actions
     * @param string $instance
     * @return array
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\InvalidConfigException
     */
    protected function resolveActions(array $actions, string $instance)
    {
        foreach ($actions as $i => $action) {
            // $action could be a string or config array
            if (!$action instanceof $instance) {
                $actions[$i] = $action = ComponentHelper::createComponent($action, $instance);

                if ($actions[$i] === null) {
                    unset($actions[$i]);
                }
            }
        }

        return array_values($actions);
    }

    /**
     * @param Objects $field
     * @param ElementInterface|Element $element
     * @return false|null|string
     */
    public function findSObjectId(Objects $field, ElementInterface $element)
    {
        return Force::getInstance()->getObjectAssociations()->getQuery()
            ->select('sObjectId')
            ->field($field->id)
            ->element($element->getId())
            ->scalar();
    }
}
