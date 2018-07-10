<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\queries;

use flipbox\ember\actions\model\ModelCreate;
use flipbox\force\Force;
use flipbox\force\records\Query;
use yii\base\BaseObject;
use yii\base\Model;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Create extends ModelCreate
{
    use traits\Populate;

    /**
     * @inheritdoc
     * @param Query $object
     * @return Query
     * @throws \flipbox\ember\exceptions\RecordNotFoundException
     */
    protected function populate(BaseObject $object): BaseObject
    {
        if (true === $this->ensureQuery($object)) {
            parent::populate($object);
            $this->populateSettings($object);
        }
        return $object;
    }

    /**
     * @inheritdoc
     * @return Query
     */
    protected function newModel(array $config = []): Model
    {
        return Force::getInstance()->getCp()->getQueryManager()->create($config);
    }

    /**
     * @inheritdoc
     * @param Query $model
     * @return bool
     * @throws \Throwable
     */
    protected function performAction(Model $model): bool
    {
        return $model->insert();
    }
}
