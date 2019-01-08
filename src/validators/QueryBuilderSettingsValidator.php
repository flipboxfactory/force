<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\validators;

use flipbox\craft\ember\validators\ModelValidator;
use flipbox\force\query\settings\DynamicQuerySettings;
use flipbox\force\query\settings\QuerySettingsInterface;
use Flipbox\Salesforce\Query\DynamicQueryBuilder;
use Flipbox\Salesforce\Query\QueryBuilderAttributeTrait;
use yii\base\Exception;

class QueryBuilderSettingsValidator extends ModelValidator
{
    use QueryBuilderAttributeTrait;

    /**
     * @var string
     */
    public $message = 'Invalid Salesforce Query';

    /**
     * The attribute containing the query builder.  Leave blank of the attribute is the query builder.
     *
     * @var string
     */
    public $attribute;

    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     * @throws Exception
     */
    public function validateAttribute($model, $attribute)
    {
        $settings = $this->getBuilderSettings($model, $attribute);

        $result = $this->validateValue($settings);
        if (!empty($result)) {
            $this->addError($model, $attribute, $result[0], $result[1]);
        }
    }

    /**
     * @param $model
     * @param $attribute
     * @return QuerySettingsInterface
     * @throws Exception
     */
    private function getBuilderSettings($model, $attribute): QuerySettingsInterface
    {
        // Load the query builder
        $this->setQuery($model->{$attribute});

        $query = $this->getQuery();

        if (!$query instanceof DynamicQueryBuilder) {
            throw new Exception("Invalid Query Type");
        }

        return new DynamicQuerySettings($query);
    }
}
