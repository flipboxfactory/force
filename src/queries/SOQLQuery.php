<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipboxfactory/craft-sortable-associations/blob/master/LICENSE
 * @link       https://github.com/flipboxfactory/craft-sortable-associations
 */

namespace flipbox\craft\salesforce\queries;

use craft\helpers\Db;
use flipbox\craft\ember\queries\ActiveQuery;
use flipbox\craft\ember\queries\AuditAttributesTrait;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SOQLQuery extends ActiveQuery
{
    use AuditAttributesTrait;

    /**
     * @var int|int[]|string|string[]|null
     */
    public $id;

    /**
     * @var string|string[]|null
     */
    public $handle;

    /**
     * @var string|string[]|null
     */
    public $name;

    /**
     * @var string|string[]|null
     */
    public $class;

    /*******************************************
     * ATTRIBUTES
     *******************************************/

    /**
     * @param int|int[]|string|string[]|null $id
     * @return $this
     */
    public function id($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param int|int[]|string|string[]|null $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->id($id);
    }

    /**
     * @param string|string[]|null $handle
     * @return $this
     */
    public function handle($handle)
    {
        $this->handle = $handle;
        return $this;
    }

    /**
     * @param string|string[]|null $handle
     * @return $this
     */
    public function setHandle($handle)
    {
        return $this->handle($handle);
    }

    /**
     * @param string|string[]|null $name
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string|string[]|null $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->name($name);
    }

    /**
     * @param string|string[]|null $class
     * @return $this
     */
    public function class($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @param string|string[]|null $class
     * @return $this
     */
    public function setClass($class)
    {
        return $this->class($class);
    }


    /*******************************************
     * PREPARE
     *******************************************/

    /**
     * @inheritdoc
     */
    public function prepare($builder)
    {
        $this->prepareAttributeConditions();
        $this->applyAuditAttributeConditions();

        return parent::prepare($builder);
    }

    /**
     * Apply environment params
     */
    protected function prepareAttributeConditions()
    {
        $attributes = ['id', 'handle', 'name', 'class'];

        foreach ($attributes as $attribute) {
            if (($value = $this->{$attribute}) !== null) {
                $this->andWhere(Db::parseParam($attribute, $value));
            }
        }
    }
}
