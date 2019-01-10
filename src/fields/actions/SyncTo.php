<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\fields\actions;

use Craft;
use craft\base\ElementInterface;
use flipbox\craft\integration\fields\actions\AbstractIntegrationAction;
use flipbox\craft\integration\fields\Integrations;
use flipbox\craft\integration\queries\IntegrationAssociationQuery;
use flipbox\craft\salesforce\queue\SyncElementToSalesforceObjectJob;
use yii\web\HttpException;

class SyncTo extends AbstractIntegrationAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('salesforce', 'Create Salesforce Object from Element');
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationMessage()
    {
        return Craft::t(
            'salesforce',
            "This element will be used to create a new Salesforce Object.  Please confirm to continue."
        );
    }

    /**
     * @inheritdoc
     * @throws HttpException
     * @throws \Throwable
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function performAction(Integrations $field, ElementInterface $element): bool
    {
        /** @var IntegrationAssociationQuery $query */
        if (null === ($query = $element->getFieldValue($field->handle))) {
            throw new HttpException(400, 'Field is not associated to element');
        }

        $job = new SyncElementToSalesforceObjectJob([
            'element' => $element,
            'field' => $field
        ]);


        if (!$job->execute(Craft::$app->getQueue())) {
            $this->setMessage("Failed to create Salesforce Object");
            return false;
        }

        $this->id = $query->select(['objectId'])->scalar();

        $this->setMessage("Created Salesforce Object successfully");
        return true;
    }
}
