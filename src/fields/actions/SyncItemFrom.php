<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\fields\actions;

use Craft;
use craft\base\ElementInterface;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use flipbox\force\records\ObjectAssociation;

class SyncItemFrom extends AbstractObjectItemAction
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
     * @throws \yii\base\InvalidConfigException
     */
    public function performAction(Objects $field, ElementInterface $element, ObjectAssociation $record): bool
    {
        if (!Force::getInstance()->getResources()->getSObject()->syncDown($element, $field)) {
            $this->setMessage("Failed to sync from Salesforce Object");
            return false;
        }

        $this->setMessage("Sync from HubSpot executed successfully");
        return true;
    }
}
