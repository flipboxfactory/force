<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\sobjects;

use flipbox\force\Force;
use flipbox\force\records\SObjectAssociation;
use yii\base\Model;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Associate extends AbstractAssociationAction
{
    /**
     * @inheritdoc
     * @param SObjectAssociation $model
     */
    protected function performAction(Model $model): bool
    {
        if (true === $this->ensureAssociation($model)) {
            return Force::getInstance()->getSObjectAssociations()->associate(
                $model
            );
        }

        return false;
    }
}
