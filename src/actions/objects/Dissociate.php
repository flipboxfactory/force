<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\objects;

use flipbox\force\Force;
use flipbox\force\records\ObjectAssociation;
use yii\base\Model;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Dissociate extends AbstractAssociationAction
{
    /**
     * @inheritdoc
     * @param ObjectAssociation $model
     * @throws \flipbox\ember\exceptions\RecordNotFoundException
     * @throws \Exception
     */
    protected function performAction(Model $model): bool
    {
        if (true === $this->ensureAssociation($model)) {
            return Force::getInstance()->getObjectAssociations()->dissociate(
                $model
            );
        }

        return false;
    }
}
