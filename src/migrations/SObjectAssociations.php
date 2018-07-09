<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\migrations;

use craft\db\Migration;
use craft\records\Element as ElementRecord;
use craft\records\Field as FieldRecord;
use craft\records\Site as SiteRecord;
use flipbox\force\records\ObjectAssociation as SObjectAssociationRecord;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SObjectAssociations extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTables();
        $this->createIndexes();
        $this->addForeignKeys();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists(SObjectAssociationRecord::tableName());
        return true;
    }

    /**
     * Creates the tables.
     *
     * @return void
     */
    protected function createTables()
    {
        $this->createTable(SObjectAssociationRecord::tableName(), [
            'sObjectId' => $this->string()->notNull(),
            'elementId' => $this->integer()->notNull(),
            'fieldId' => $this->integer()->notNull(),
            'siteId' => $this->integer()->notNull(),
            'sortOrder' => $this->smallInteger()->unsigned(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);
    }

    /**
     * Creates the indexes.
     *
     * @return void
     */
    protected function createIndexes()
    {
        $this->addPrimaryKey(
            null,
            SObjectAssociationRecord::tableName(),
            [
                'elementId',
                'sObjectId',
                'fieldId',
                'siteId'
            ]
        );
        $this->createIndex(
            null,
            SObjectAssociationRecord::tableName(),
            'sObjectId',
            false
        );
    }

    /**
     * Adds the foreign keys.
     *
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(
            null,
            SObjectAssociationRecord::tableName(),
            'elementId',
            ElementRecord::tableName(),
            'id',
            'CASCADE',
            null
        );
        $this->addForeignKey(
            null,
            SObjectAssociationRecord::tableName(),
            'siteId',
            SiteRecord::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            null,
            SObjectAssociationRecord::tableName(),
            'fieldId',
            FieldRecord::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );
    }
}
