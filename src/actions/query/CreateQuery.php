<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\query;

use Craft;
use flipbox\craft\ember\actions\records\CreateRecord;
use flipbox\force\records\QueryBuilder;
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
     * @param QueryBuilder $record
     * @return QueryBuilder
     */
    protected function populate(ActiveRecord $record): ActiveRecord
    {
        parent::populate($record);
        return $this->populateSettings($record);
    }

    /**
     * @param array $config
     * @return QueryBuilder
     * @throws \yii\base\InvalidConfigException
     */
    protected function newRecord(array $config = []): ActiveRecord
    {
        $class = Craft::$app->getRequest()->getBodyParam('class', QueryBuilder::class);

        /** @var QueryBuilder $record */
        $record = Instance::ensure(
            $class,
            QueryBuilder::class
        );

        $record->setAttributes($config);

        return $record;
    }
}
