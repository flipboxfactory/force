<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\queries;

use flipbox\ember\actions\model\ModelDelete;
use flipbox\force\records\Query;
use yii\base\Model;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Delete extends ModelDelete
{
    use traits\Lookup;

    /**
     * @inheritdoc
     */
    public function run($query)
    {
        return parent::run($query);
    }

    /**
     * @inheritdoc
     * @param Query $model
     * @throws \Throwable
     */
    protected function performAction(Model $model): bool
    {
        return $model->delete();
    }
}
