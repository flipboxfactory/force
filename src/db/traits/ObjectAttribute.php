<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\db\traits;

use craft\helpers\Db;
use yii\db\Expression;

trait ObjectAttribute
{
    /**
     * @var string|string[]|null
     */
    public $object;

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
    public function setObjectId($value)
    {
        return $this->setObject($value);
    }

    /**
     * @param string|string[]|null $value
     * @return static
     */
    public function objectId($value)
    {
        return $this->setObject($value);
    }

    /**
     * @param string|string[]|null $value
     * @return static
     */
    public function setObject($value)
    {
        $this->object = $value;
        return $this;
    }

    /**
     * @param string|string[]|null $value
     * @return static
     */
    public function object($value)
    {
        return $this->setObject($value);
    }

    /**
     *  Apply query specific conditions
     */
    protected function applyObjectConditions()
    {
        if ($this->object !== null) {
            $this->andWhere(Db::parseParam('objectId', $this->object));
        }
    }
}
