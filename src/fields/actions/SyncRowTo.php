<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\fields\actions;

use Craft;
use craft\base\ElementInterface;
use flipbox\force\criteria\SObjectCriteria;
use flipbox\force\fields\SObjects;
use flipbox\force\Force;
use flipbox\force\transformers\collections\TransformerCollection;

class SyncRowTo extends AbstractSObjectRowAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('force', 'Sync To Salesforce');
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationMessage()
    {
        return Craft::t('force', "Performing a sync will transmit any unsaved data.  Please confirm to continue.");
    }

    /**
     * @inheritdoc
     */
    public function performAction(SObjects $field, ElementInterface $element, SObjectCriteria $criteria): bool
    {
        // Ensure consistent transformers
        $criteria->transformer(TransformerCollection::class);

        if (!Force::getInstance()->getElements()->syncUp(
            $element,
            $field,
            $criteria
        )) {
            $this->setMessage("Failed to sync to Salesforce Object");
            return false;
        }

        $this->setMessage("Sync to Salesforce executed successfully");
        return true;
    }
}
