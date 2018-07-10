<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\fields\actions;

use Craft;
use craft\base\ElementInterface;
use flipbox\force\db\ObjectAssociationQuery;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use yii\web\HttpException;

class SyncTo extends AbstractObjectAction
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
        return Craft::t(
            'force',
            "This element will be used to create a new Salesforce Object.  Please confirm to continue."
        );
    }

    /**
     * @param Objects $field
     * @param ElementInterface $element
     * @return bool
     * @throws HttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function performAction(Objects $field, ElementInterface $element): bool
    {
        /** @var ObjectAssociationQuery $query */
        if (null === ($query = $element->getFieldValue($field->handle))) {
            throw new HttpException(400, 'Field is not associated to element');
        }

        if (!Force::getInstance()->getResources()->getObject()->syncUp(
            $element,
            $field
        )) {
            $this->setMessage("Failed to sync from HubSpot Object");
            return false;
        }

        $element->setFieldValue($field->handle, null);

        $this->id = $query->select(['objectId'])->scalar();

        $this->setMessage("Sync to Salesforce executed successfully");
        return true;
    }
}
