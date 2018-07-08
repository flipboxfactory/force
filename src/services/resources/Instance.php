<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services\resources;

use flipbox\ember\helpers\ObjectHelper;
use flipbox\force\criteria\InstanceCriteria;
use flipbox\force\criteria\InstanceCriteriaInterface;
use flipbox\force\transformers\collections\DynamicTransformerCollection;
use flipbox\force\transformers\collections\TransformerCollectionInterface;
use flipbox\force\transformers\DynamicModelSuccess;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Instance extends Component
{
    use traits\DescribeTrait,
        traits\LimitsTrait,
        traits\ResourcesTrait;

    /**
     * The resource name
     */
    const SALESFORCE_RESOURCE = 'instance';

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
     * @return InstanceCriteria
     */
    public function getCriteria(array $criteria = []): InstanceCriteriaInterface
    {
        $object = new InstanceCriteria();

        ObjectHelper::populate(
            $object,
            $criteria
        );

        return $object;
    }
}
