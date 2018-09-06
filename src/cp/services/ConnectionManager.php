<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\services;

use flipbox\ember\exceptions\ObjectNotFoundException;
use flipbox\ember\services\traits\records\AccessorByString;
use flipbox\force\cp\connections\ConnectionTypeInterface;
use flipbox\force\cp\connections\Patron as PatronSettings;
use flipbox\force\patron\connections\AccessTokenConnection;
use flipbox\force\records\Connection as ConnectionRecord;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method ConnectionRecord create(array $attributes = [])
 * @method ConnectionRecord find($identifier)
 * @method ConnectionRecord get($identifier)
 * @method ConnectionRecord findByString($identifier)
 * @method ConnectionRecord getByString($identifier)
 * @method ConnectionRecord findByCondition($condition = [])
 * @method ConnectionRecord getByCondition($condition = [])
 * @method ConnectionRecord findByCriteria($criteria = [])
 * @method ConnectionRecord getByCriteria($criteria = [])
 * @method ConnectionRecord[] findAllByCondition($condition = [])
 * @method ConnectionRecord[] getAllByCondition($condition = [])
 * @method ConnectionRecord[] findAllByCriteria($criteria = [])
 * @method ConnectionRecord[] getAllByCriteria($criteria = [])
 */
class ConnectionManager extends Component
{
    use AccessorByString;

    /**
     * @inheritdoc
     */
    public static function recordClass(): string
    {
        return ConnectionRecord::class;
    }

    /**
     * @inheritdoc
     */
    protected function stringProperty(): string
    {
        return 'handle';
    }

    /**
     * @return ConnectionTypeInterface[]
     */
    public function getTypes(): array
    {
        return [
            AccessTokenConnection::class => new PatronSettings
        ];
    }

    /**
     * @param string $class
     * @return ConnectionTypeInterface|null
     */
    public function findType(string $class)
    {
        $types = $this->getTypes();
        return $types[$class] ?? null;
    }

    /**
     * @param string $class
     * @return ConnectionTypeInterface
     * @throws ObjectNotFoundException
     */
    public function getType(string $class): ConnectionTypeInterface
    {
        if (null === ($types = $this->findType($class))) {
            throw new ObjectNotFoundException("Unable to find connection type");
        }

        return $types;
    }
}
