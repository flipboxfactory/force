<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services\resources;

use flipbox\ember\helpers\ObjectHelper;
use flipbox\force\criteria\QueryCriteria;
use flipbox\force\Force;
use flipbox\force\helpers\TransformerHelper;
use Flipbox\Salesforce\Connections\ConnectionInterface;
use Flipbox\Salesforce\Query\QueryBuilderInterface;
use Flipbox\Salesforce\Resources\Query as QueryResource;
use Flipbox\Salesforce\Transformers\Collections\TransformerCollectionInterface;
use Psr\SimpleCache\CacheInterface;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Query extends Component
{
    /*******************************************
     * CRITERIA
     *******************************************/

    /**
     * @param array $criteria
     * @return QueryCriteria
     */
    public function getCriteria(array $criteria = [])
    {
        $object = new QueryCriteria();

        ObjectHelper::populate(
            $object,
            $criteria
        );

        return $object;
    }


    /*******************************************
     * FETCH
     *******************************************/

    /**
     * @param QueryBuilderInterface $query
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface $cache
     * @param TransformerCollectionInterface|null $transformer
     * @return mixed
     */
    public function fetch(
        QueryBuilderInterface $query,
        ConnectionInterface $connection,
        CacheInterface $cache,
        TransformerCollectionInterface $transformer = null
    ) {
        return (new QueryResource(
            $query->build(),
            $connection,
            $cache,
            TransformerHelper::populateTransformerCollection($transformer, [
                'resource' => [QueryResource::class],
                'handle' => ['query']
            ]),
            Force::getInstance()->getLogger()
        ));
    }

    /**
     * @param QueryCriteria $criteria
     * @return mixed
     */
    public function fetchFromCriteria(
        QueryCriteria $criteria
    ) {
        return $this->fetch(
            $criteria->getQuery(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }
}
