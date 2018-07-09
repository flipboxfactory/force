<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services\resources;

use flipbox\ember\helpers\ObjectHelper;
use flipbox\force\criteria\ObjectAccessorCriteria;
use flipbox\force\criteria\ObjectAccessorCriteriaInterface;
use flipbox\force\criteria\ObjectMutatorCriteria;
use flipbox\force\criteria\ObjectMutatorCriteriaInterface;
use flipbox\force\transformers\collections\DynamicTransformerCollection;
use flipbox\force\transformers\collections\TransformerCollectionInterface;
use flipbox\force\transformers\DynamicModelSuccess;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Object extends Component
{
    use traits\DescribeObjectTrait,
        traits\SyncElementTrait,
        traits\CreateObjectTrait,
        traits\ReadObjectTrait,
        traits\UpdateObjectTrait,
        traits\DeleteObjectTrait,
        traits\UpsertObjectTrait;

    /**
     * The resource name
     */
    const SALESFORCE_RESOURCE = 'object';

    /**
     * @return array
     */
    public static function defaultTransformer()
    {
        return [
            'class' => DynamicTransformerCollection::class,
            'handle' => self::SALESFORCE_RESOURCE,
            'transformers' => [
                TransformerCollectionInterface::SUCCESS_KEY => [
                    'class' => DynamicModelSuccess::class,
                    'resource' => self::SALESFORCE_RESOURCE
                ]
            ]
        ];
    }

    /*******************************************
     * CRITERIA
     *******************************************/

    /**
     * @param array $criteria
     * @return ObjectAccessorCriteria
     */
    public function getAccessorCriteria(array $criteria = []): ObjectAccessorCriteriaInterface
    {
        $object = new ObjectAccessorCriteria();

        ObjectHelper::populate(
            $object,
            $criteria
        );

        return $object;
    }

    /**
     * @param array $criteria
     * @return ObjectMutatorCriteria
     */
    public function getMutatorCriteria(array $criteria = []): ObjectMutatorCriteriaInterface
    {
        $object = new ObjectMutatorCriteria();

        ObjectHelper::populate(
            $object,
            $criteria
        );

        return $object;
    }
}
