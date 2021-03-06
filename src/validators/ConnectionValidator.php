<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\validators;

use Craft;
use flipbox\force\connections\ConnectionInterface;
use yii\validators\Validator;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ConnectionValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $class = $model->$attribute;

        // Handles are always required, so if it's blank, the required validator will catch this.
        if ($class) {
            if (!$class instanceof ConnectionInterface &&
                !is_subclass_of($class, ConnectionInterface::class)
            ) {
                $message = Craft::t(
                    'force',
                    '“{class}” is a not a valid connection.',
                    ['class' => $class]
                );
                $this->addError($model, $attribute, $message);
            }
        }
    }
}
