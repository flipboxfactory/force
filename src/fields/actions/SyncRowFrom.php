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
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use flipbox\force\transformers\collections\TransformerCollection;

class SyncRowFrom extends AbstractSObjectRowAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('force', 'Sync From Salesforce');
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationMessage()
    {
        return Craft::t('force', "Performing a sync will override any unsaved data.  Please confirm to continue.");
    }

    /**
     * @inheritdoc
     */
    public function performAction(Objects $field, ElementInterface $element, SObjectCriteria $criteria): bool
    {
        // Ensure consistent transformers
        $criteria->transformer(TransformerCollection::class);

        if (!Force::getInstance()->getElements()->syncDown(
            $element,
            $field,
            $criteria
        )) {
            $this->setMessage("Failed to sync from Salesforce Object");
            return false;
        }

        $this->setMessage("Sync from Salesforce executed successfully");
        return true;
    }
}
