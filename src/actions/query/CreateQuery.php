<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\actions\query;

use Craft;
use flipbox\craft\ember\actions\records\CreateRecord;
use flipbox\craft\salesforce\records\SOQL;
use yii\db\ActiveRecord;
use yii\di\Instance;

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
    public $validBodyParams = [
        'name',
        'handle',
        'soql'
    ];

    /**
     * @inheritdoc
     * @param SOQL $record
     * @return SOQL
     */
    protected function populate(ActiveRecord $record): ActiveRecord
    {
        parent::populate($record);
        return $this->populateSettings($record);
    }

    /**
     * @param array $config
     * @return SOQL
     * @throws \yii\base\InvalidConfigException
     */
    protected function newRecord(array $config = []): ActiveRecord
    {
        $class = Craft::$app->getRequest()->getBodyParam('class', SOQL::class);

        /** @var SOQL $record */
        $record = Instance::ensure(
            $class,
            SOQL::class
        );

        $record->setAttributes($config);

        return $record;
    }
}
