<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\validators;

use Craft;
use flipbox\craft\ember\validators\ModelValidator;
use Flipbox\Salesforce\Query\QueryBuilderInterface;

class QueryBuilderValidator extends ModelValidator
{
    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $class = $model->$attribute;

        // Handles are always required, so if it's blank, the required validator will catch this.
        if ($class) {
            if (!$class instanceof QueryBuilderInterface &&
                !is_subclass_of($class, QueryBuilderInterface::class)
            ) {
                $message = Craft::t(
                    'salesforce',
                    '“{class}” is a not a valid query builder.',
                    ['class' => $class]
                );
                $this->addError($model, $attribute, $message);
            }
        }
    }
}
