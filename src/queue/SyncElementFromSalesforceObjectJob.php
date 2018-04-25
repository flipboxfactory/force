<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\queue;

use flipbox\force\Force;

/**
 * Sync a Salesforce Object to a Craft Element
 */
class SyncElementFromSalesforceObjectJob extends AbstractSyncElementJob
{
    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        return Force::getInstance()->getElements()->syncDown(
            $this->getElement(),
            $this->getField()
        );
    }
}
