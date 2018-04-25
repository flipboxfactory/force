<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\db\traits;

use craft\helpers\Db;
use yii\db\Expression;

trait SObjectAttribute
{
    /**
     * @var string|string[]|null
     */
    public $sObject;

    /**
     * Adds an additional WHERE condition to the existing one.
     * The new condition and the existing one will be joined using the `AND` operator.
     * @param string|array|Expression $condition the new WHERE condition. Please refer to [[where()]]
     * on how to specify this parameter.
     * @param array $params the parameters (name => value) to be bound to the query.
     * @return $this the query object itself
     * @see where()
     * @see orWhere()
     */
    abstract public function andWhere($condition, $params = []);

    /**
     * @param string|string[]|null $value
     * @return static
     */
    public function setSObjectId($value)
    {
        return $this->setSObject($value);
    }

    /**
     * @param string|string[]|null $value
     * @return static
     */
    public function sObjectId($value)
    {
        return $this->setSObject($value);
    }

    /**
     * @param string|string[]|null $value
     * @return static
     */
    public function setSObject($value)
    {
        $this->sObject = $value;
        return $this;
    }

    /**
     * @param string|string[]|null $value
     * @return static
     */
    public function sObject($value)
    {
        return $this->setSObject($value);
    }

    /**
     *  Apply query specific conditions
     */
    protected function applySObjectConditions()
    {
        if ($this->sObject !== null) {
            $this->andWhere(Db::parseParam('sObjectId', $this->sObject));
        }
    }
}
