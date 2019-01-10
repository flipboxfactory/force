<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\fields\actions;

use Craft;
use craft\base\ElementInterface;
use flipbox\craft\integration\fields\actions\AbstractIntegrationItemAction;
use flipbox\craft\integration\fields\Integrations;
use flipbox\craft\integration\records\IntegrationAssociation;
use flipbox\craft\salesforce\queue\SyncElementToSalesforceObjectJob;

class SyncItemTo extends AbstractIntegrationItemAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('salesforce', 'Sync To Salesforce');
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationMessage()
    {
        return Craft::t('salesforce', "Performing a sync will transmit any unsaved data.  Please confirm to continue.");
    }

    /**
     * @inheritdoc
     * @throws \Throwable
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function performAction(Integrations $field, ElementInterface $element, IntegrationAssociation $record): bool
    {
        $job = new SyncElementToSalesforceObjectJob([
            'element' => $element,
            'field' => $field
        ]);


        if (!$job->execute(Craft::$app->getQueue())) {
            $this->setMessage("Failed to sync to Salesforce Object");
            return false;
        }

        $this->setMessage("Sync to Salesforce executed successfully");
        return true;
    }
}
