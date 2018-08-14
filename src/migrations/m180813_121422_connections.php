<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\migrations;

use craft\db\Migration;
use flipbox\force\records\Connection as ConnectionRecord;

class m180813_121422_connections extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            ConnectionRecord::tableName(),
            [
                'id' => $this->primaryKey(),
                'handle' => $this->string()->notNull(),
                'class' => $this->string()->notNull(),
                'settings' => $this->text(),
                'enabled' => $this->boolean(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'dateCreated' => $this->dateTime()->notNull(),
                'uid' => $this->uid()
            ]
        );

        $this->createIndex(
            $this->db->getIndexName(ConnectionRecord::tableName(), 'handle', true),
            ConnectionRecord::tableName(),
            'handle',
            true
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists(ConnectionRecord::tableName());
        return true;
    }
}
