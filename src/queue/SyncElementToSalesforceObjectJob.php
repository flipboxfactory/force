<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\queue;

use flipbox\force\Force;

/**
 * Sync a Craft Element to a Salesforce Object
 */
class SyncElementToSalesforceObjectJob extends AbstractSyncElementJob
{
    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue)
    {
        return Force::getInstance()->getResources()->getObject()->syncUp(
            $this->getElement(),
            $this->getField()
        );
    }
}
