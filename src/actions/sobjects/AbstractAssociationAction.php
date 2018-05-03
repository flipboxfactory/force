<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\sobjects;

use Craft;
use craft\base\FieldInterface;
use flipbox\ember\actions\model\traits\Manage;
use flipbox\ember\exceptions\RecordNotFoundException;
use flipbox\force\fields\SObjects;
use flipbox\force\Force;
use flipbox\force\records\SObjectAssociation;
use yii\base\Action;
use yii\base\Model;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since  1.0.0
 */
abstract class AbstractAssociationAction extends Action
{
    use Manage;

    /**
     * @param string $field
     * @param string $element
     * @param string $sObjectId
     * @param int|null $siteId
     * @param int|null $sortOrder
     * @return mixed
     * @throws HttpException
     */
    public function run(
        string $field,
        string $element,
        string $sObjectId,
        int $siteId = null,
        int $sortOrder = null
    ) {
        // Resolve Field
        $sObjectField = $this->resolveField($field);

        // Resolve Element
        if (null === ($sourceElement = Craft::$app->getElements()->getElementById($element))) {
            return $this->handleInvalidElementResponse($element);
        }

        // Resolve Site Id
        if (null === $siteId) {
            $siteId = Craft::$app->getSites()->currentSite->id;
        }

        return $this->runInternal(Force::getInstance()->getSObjectAssociations()->create([
            'sObjectId' => $sObjectId,
            'elementId' => $sourceElement->getId(),
            'fieldId' => $sObjectField->id,
            'siteId' => $siteId,
            'sortOrder' => $sortOrder
        ]));
    }

    /**
     * @param string $field
     * @return SObjects
     * @throws HttpException
     */
    protected function resolveField(string $field): SObjects
    {
        if (null === ($sObjectField = Force::getInstance()->getSObjectsField()->findById($field))) {
            return $this->handleInvalidFieldResponse($field);
        }

        return $sObjectField;
    }

    /**
     * @param Model $model
     * @return bool
     * @throws RecordNotFoundException
     */
    protected function ensureAssociation(Model $model): bool
    {
        if (!$model instanceof SObjectAssociation) {
            throw new RecordNotFoundException(sprintf(
                "SObject Association must be an instance of '%s', '%s' given.",
                SObjectAssociation::class,
                get_class($model)
            ));
        }

        return true;
    }

    /**
     * @param int $fieldId
     * @throws HttpException
     */
    protected function handleInvalidFieldResponse(int $fieldId)
    {
        throw new HttpException(sprintf(
            "The provided field '%s' must be an instance of '%s'",
            (string)$fieldId,
            (string)FieldInterface::class
        ));
    }

    /**
     * @param int $elementId
     * @throws HttpException
     */
    protected function handleInvalidElementResponse(int $elementId)
    {
        throw new HttpException(sprintf(
            "The provided source '%s' must be an instance of '%s'",
            (string)$elementId,
            (string)SObjects::class
        ));
    }
}
