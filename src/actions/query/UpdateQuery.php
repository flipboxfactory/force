<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\query;

use flipbox\craft\ember\actions\records\UpdateRecord;
use flipbox\force\records\QueryBuilder;
use yii\db\ActiveRecord;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class UpdateQuery extends UpdateRecord
{
    use PopulateQueryTrait;

    /**
     * @inheritdoc
     */
    public $validBodyParams = [
        'name',
        'handle',
        'soql'
    ];

    /**
     * @inheritdoc
     */
    public function run($query)
    {
        return parent::run($query);
    }

    /**
     * @inheritdoc
     * @param QueryBuilder $record
     * @return QueryBuilder
     */
    protected function populate(ActiveRecord $record): ActiveRecord
    {
        $this->populateSettings($record);
        return parent::populate($record);
    }

    /**
     * @inheritdoc
     * @return QueryBuilder
     */
    protected function find($identifier)
    {
        return QueryBuilder::findOne($identifier);
    }
}
