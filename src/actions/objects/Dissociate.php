<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\objects;

use flipbox\craft\integration\actions\objects\Dissociate as DissociateIntegration;
use flipbox\craft\integration\services\IntegrationAssociations;
use flipbox\force\Force;
use flipbox\force\services\ObjectAssociations;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Dissociate extends DissociateIntegration
{
    /**
     * @inheritdoc
     * @return ObjectAssociations
     */
    protected function associationService(): IntegrationAssociations
    {
        return Force::getInstance()->getObjectAssociations();
    }
}
