<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\actions\query;

use flipbox\craft\ember\actions\records\UpdateRecord;
use flipbox\craft\salesforce\records\SOQL;
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
     * @param SOQL $record
     * @return SOQL
     */
    protected function populate(ActiveRecord $record): ActiveRecord
    {
        $this->populateSettings($record);
        return parent::populate($record);
    }

    /**
     * @inheritdoc
     * @return SOQL
     */
    protected function find($identifier)
    {
        return SOQL::findOne($identifier);
    }
}
