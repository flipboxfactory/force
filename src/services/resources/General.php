<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services\resources;

use flipbox\ember\helpers\ObjectHelper;
use flipbox\force\criteria\ListCriteria;
use flipbox\force\Force;
use flipbox\force\helpers\TransformerHelper;
use Flipbox\Salesforce\Connections\ConnectionInterface;
use Flipbox\Salesforce\Resources\Describe as DescribeResource;
use Flipbox\Salesforce\Resources\Limits as LimitsResource;
use Flipbox\Salesforce\Resources\Resources as ResourcesResource;
use Flipbox\Salesforce\Transformers\Collections\TransformerCollectionInterface;
use Psr\SimpleCache\CacheInterface;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class General extends Component
{
    /*******************************************
     * CRITERIA
     *******************************************/

    /**
     * @param array $criteria
     * @return ListCriteria
     */
    public function getCriteria(array $criteria = []): ListCriteria
    {
        $object = new ListCriteria();

        ObjectHelper::populate(
            $object,
            $criteria
        );

        return $object;
    }

    /*******************************************
     * DESCRIBE
     *******************************************/
    /**
     * @param ConnectionInterface $connection
     * @param CacheInterface $cache
     * @param TransformerCollectionInterface|null $transformer
     * @return DescribeResource
     */
    public function describe(
        ConnectionInterface $connection,
        CacheInterface $cache,
        TransformerCollectionInterface $transformer = null
    ): DescribeResource {
        return (new DescribeResource(
            $connection,
            $cache,
            TransformerHelper::populateTransformerCollection($transformer, [
                'resource' => [DescribeResource::class],
                'handle' => ['describe']
            ])
        ));
    }

    /**
     * @param ListCriteria $criteria
     * @return DescribeResource
     */
    public function describeFromCriteria(
        ListCriteria $criteria
    ) {
        return $this->describe(
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }


    /*******************************************
     * LIMITS
     *******************************************/
    /**
     * @param ConnectionInterface $connection
     * @param CacheInterface $cache
     * @param TransformerCollectionInterface|null $transformer
     * @return LimitsResource
     */
    public function getLimits(
        ConnectionInterface $connection,
        CacheInterface $cache,
        TransformerCollectionInterface $transformer = null
    ) {
        return (new LimitsResource(
            $connection,
            $cache,
            TransformerHelper::populateTransformerCollection($transformer, [
                'resource' => [LimitsResource::class],
                'handle' => ['limits']
            ]),
            Force::getInstance()->getLogger()
        ));
    }

    /**
     * @param ListCriteria $criteria
     * @return LimitsResource
     */
    public function getLimitsFromCriteria(
        ListCriteria $criteria
    ) {
        return $this->getLimits(
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }

    /*******************************************
     * LIMITS
     *******************************************/
    /**
     * @param ConnectionInterface $connection
     * @param CacheInterface $cache
     * @param TransformerCollectionInterface|null $transformer
     * @return ResourcesResource
     */
    public function getResources(
        ConnectionInterface $connection,
        CacheInterface $cache,
        TransformerCollectionInterface $transformer = null
    ) {
        return (new ResourcesResource(
            $connection,
            $cache,
            TransformerHelper::populateTransformerCollection($transformer, [
                'resource' => [ResourcesResource::class],
                'handle' => ['resources']
            ]),
            Force::getInstance()->getLogger()
        ));
    }

    /**
     * @param ListCriteria $criteria
     * @return ResourcesResource
     */
    public function getResourcesFromCriteria(
        ListCriteria $criteria
    ) {
        return $this->getResources(
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }
}
