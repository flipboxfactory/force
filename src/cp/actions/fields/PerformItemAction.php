<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\actions\fields;

use flipbox\craft\integration\actions\fields\PerformItemAction as PerformItemActionIntegration;
use flipbox\craft\integration\services\IntegrationField;
use flipbox\force\Force;
use flipbox\force\services\ObjectsField;

/**
 * Performs an action on an individual field row
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class PerformItemAction extends PerformItemActionIntegration
{
    /**
     * @inheritdoc
     * @return ObjectsField
     */
    protected function fieldService(): IntegrationField
    {
        return Force::getInstance()->getObjectsField();
    }
}
