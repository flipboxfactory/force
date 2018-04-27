<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\db;

use Craft;
use craft\db\QueryAbortedException;
use craft\helpers\Db;
use flipbox\craft\sortable\associations\db\SortableAssociationQuery;
use flipbox\craft\sortable\associations\db\traits\SiteAttribute;
use flipbox\ember\db\traits\ElementAttribute;
use flipbox\force\Force;
use flipbox\force\records\SObjectAssociation;

/**
 * @method SObjectAssociation[] getCachedResult()
 */
class SObjectAssociationQuery extends SortableAssociationQuery
{
    use traits\FieldAttribute,
        traits\SObjectAttribute,
        ElementAttribute,
        SiteAttribute;

    /**
     * @inheritdoc
     */
    public function __construct($modelClass, $config = [])
    {
        Force::getInstance()->getSObjectAssociations()->ensureTableExists();
        parent::__construct($modelClass, $config);
    }

    /**
     * @inheritdoc
     */
    protected function fixedOrderColumn(): string
    {
        return 'sObjectId';
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        return Craft::configure(
            $this,
            $config
        );
    }

    /**
     * @inheritdoc
     *
     * @throws QueryAbortedException if it can be determined that there won’t be any results
     */
    public function prepare($builder)
    {
        // Is the query already doomed?
        if (($this->field !== null && empty($this->field)) ||
            ($this->sObject !== null && empty($this->sObject)) ||
            ($this->element !== null && empty($this->element))
        ) {
            throw new QueryAbortedException();
        }

        $this->applyConditions();
        $this->applySiteConditions();
        $this->applySObjectConditions();
        $this->applyFieldConditions();

        return parent::prepare($builder);
    }

    /**
     *  Apply query specific conditions
     */
    protected function applyConditions()
    {
        if ($this->element !== null) {
            $this->andWhere(Db::parseParam('elementId', $this->parseElementValue($this->element)));
        }
    }
}
