<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\fields\actions;

use Craft;
use craft\base\ElementInterface;
use flipbox\force\db\SObjectFieldQuery;
use flipbox\force\fields\SObjects;
use flipbox\force\Force;
use flipbox\force\transformers\collections\AdminTransformerCollection;
use yii\web\HttpException;

class SyncTo extends AbstractSObjectAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('force', 'Create Salesforce Object from Element');
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationMessage()
    {
        return Craft::t('force', "This element will be used to create a new Salesforce Object.  Please confirm to continue.");
    }

    /**
     * @inheritdoc
     */
    public function performAction(SObjects $field, ElementInterface $element): bool
    {
        // Assemble request criteria
        $criteria = Force::getInstance()->getResources()->getSObject()->getCriteria([
            'transformer' => AdminTransformerCollection::class,
            'id' => false
        ]);

        if (!Force::getInstance()->getElements()->syncUp(
            $element,
            $field,
            $criteria
        )) {
            $this->setMessage("Failed to sync from Salesforce Object");
            return false;
        }

        $element->setFieldValue($field->handle, null);

        /** @var SObjectFieldQuery $query */
        if (null === ($query = $element->getFieldValue($field->handle))) {
            throw new HttpException(400, 'Field is not associated to element');
        }

        $this->id = $query->select(['sObjectId'])->scalar();

        $this->setMessage("Sync to Salesforce executed successfully");
        return true;
    }
}
