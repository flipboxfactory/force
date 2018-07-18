<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\actions\fields;

use flipbox\craft\integration\actions\fields\CreateItem as CreateItemIntegration;
use flipbox\craft\integration\services\IntegrationAssociations;
use flipbox\force\Force;
use flipbox\force\services\ObjectAssociations;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class CreateItem extends CreateItemIntegration
{
    /**
     * @inheritdoc
     * @return ObjectAssociations
     */
    public function associationService(): IntegrationAssociations
    {
        return Force::getInstance()->getObjectAssociations();
    }
}
