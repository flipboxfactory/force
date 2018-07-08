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
use flipbox\craft\sortable\associations\db\SortableAssociationQueryInterface;
use flipbox\craft\sortable\associations\db\traits\SiteAttribute;
use flipbox\ember\db\CacheableQuery;
use flipbox\ember\db\traits\ElementAttribute;
use flipbox\ember\db\traits\PopulateObject;
use flipbox\force\criteria\SObjectCriteria;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use flipbox\force\records\SObjectAssociation;
use yii\base\BaseObject;

/**
 * @method SObjectAssociation[] getCachedResult()
 */
class SObjectFieldQuery extends CacheableQuery implements SortableAssociationQueryInterface
{
    use traits\SObjectAttribute,
        ElementAttribute,
        SiteAttribute,
        PopulateObject;

    /**
     * @var Objects
     */
    private $field;

    /**
     * @inheritdoc
     * @param Objects $field
     */
    public function __construct(Objects $field, $config = [])
    {
        $this->field = $field;
        Force::getInstance()->getObjectAssociations()->ensureTableExists();
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function fixedOrderColumn(): string
    {
        return 'sObjectId';
    }

    /**
     * @return Objects
     */
    public function getField(): Objects
    {
        return $this->field;
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
        if (($this->sObject !== null && empty($this->sObject)) ||
            ($this->element !== null && empty($this->element))
        ) {
            throw new QueryAbortedException();
        }

        $this->from = [SObjectAssociation::tableName()];

        $this->applyConditions();
        $this->applySiteConditions();
        $this->applySObjectConditions();

        $this->andWhere(Db::parseParam('fieldId', $this->field->id));

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

    /*******************************************
     * POPULATE OBJECT
     *******************************************/

    /**
     * @inheritdoc
     */
    public function one($db = null)
    {
        $row = parent::one($db);

        if ($row === false || $row === null || $row instanceof SObjectCriteria) {
            return $row;
        }

        return $this->createObject($row);
    }

    /**
     * @param $row
     * @return BaseObject
     */
    protected function createObject($row): BaseObject
    {
        return $this->field->createCriteria([
            'id' => $row['sObjectId'] ?? '__UNKNOWN_ID__'
        ]);
    }
}
