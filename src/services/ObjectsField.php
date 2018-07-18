<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services;

use flipbox\craft\integration\services\IntegrationAssociations;
use flipbox\craft\integration\services\IntegrationField;
use flipbox\force\fields\actions\SyncItemFrom;
use flipbox\force\fields\actions\SyncItemTo;
use flipbox\force\fields\actions\SyncTo;
use flipbox\force\Force;
use flipbox\force\records\ObjectAssociation;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectsField extends IntegrationField
{
    /**
     * @inheritdoc
     */
    protected $defaultAvailableActions = [
        SyncTo::class
    ];

    /**
     * @inheritdoc
     */
    protected $defaultAvailableItemActions = [
        SyncItemFrom::class,
        SyncItemTo::class,
    ];

    /**
     * @inheritdoc
     * @return ObjectAssociations
     */
    protected function associationService(): IntegrationAssociations
    {
        return Force::getInstance()->getObjectAssociations();
    }

    /**
     * @inheritdoc
     */
    protected static function tableAlias(): string
    {
        return ObjectAssociation::tableAlias();
    }
}
