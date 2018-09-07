<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\connections;

use flipbox\craft\integration\records\IntegrationConnection;
use flipbox\ember\actions\record\RecordDelete;
use flipbox\force\records\Query;
use yii\db\ActiveRecord;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Delete extends RecordDelete
{
    use traits\Lookup;

    /**
     * @inheritdoc
     */
    public function run($connection)
    {
        return parent::run($connection);
    }

    /**
     * @inheritdoc
     * @param Query $record
     * @throws \Throwable
     */
    protected function performAction(ActiveRecord $record): bool
    {
        if (!$record instanceof IntegrationConnection) {
            return false;
        }

        return $record->delete();
    }
}
