<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\query;

use flipbox\craft\ember\actions\records\CreateRecord;
use flipbox\force\records\SOQL;
use yii\db\ActiveRecord;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class CreateQuery extends CreateRecord
{
    use PopulateQueryTrait;

    /**
     * @inheritdoc
     */
    protected $validBodyParams = [
        'name',
        'handle'
    ];

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
    protected function newRecord(array $config = []): ActiveRecord
    {
        $record = new SOQL();
        $record->setAttributes($config);
        return $record;
    }
}
