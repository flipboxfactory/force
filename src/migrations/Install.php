<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\migrations;

use craft\db\Migration;
use flipbox\craft\salesforce\records\SOQL as QueryRecord;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTables();
        $this->createIndexes();
        $this->addForeignKeys();

        if (false === (new ObjectAssociations())->safeUp()) {
            return false;
        };

        if (false === (new m180813_121422_connections())->safeUp()) {
            return false;
        };

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists(QueryRecord::tableName());

        if (false === (new ObjectAssociations())->safeDown()) {
            return false;
        };

        if (false === (new m180813_121422_connections())->safeDown()) {
            return false;
        };

        return true;
    }

    /**
     * Creates the tables.
     *
     * @return void
     */
    protected function createTables()
    {
        $this->createTable(QueryRecord::tableName(), [
            'id' => $this->primaryKey(),
            'handle' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'class' => $this->string()->notNull(),
            'settings' => $this->string(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);
    }

    /**
     * Creates the indexes.
     *
     * @return void
     */
    protected function createIndexes()
    {
        $this->createIndex(
            $this->db->getIndexName(
                QueryRecord::tableName(),
                'handle',
                true
            ),
            QueryRecord::tableName(),
            'handle',
            true
        );
    }

    /**
     * Adds the foreign keys.
     *
     * @return void
     */
    protected function addForeignKeys()
    {
    }
}
