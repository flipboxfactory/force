<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\queries;

use flipbox\ember\actions\model\ModelUpdate;
use flipbox\force\records\Query;
use yii\base\BaseObject;
use yii\base\Model;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Update extends ModelUpdate
{
    use traits\Lookup, traits\Populate;

    /**
     * @inheritdoc
     */
    public function run($query)
    {
        return parent::run($query);
    }

    /**
     * @inheritdoc
     * @param Query $object
     * @return Query
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
     * @param Query $model
     */
    protected function performAction(Model $model): bool
    {
        return $model->update();
    }
}
