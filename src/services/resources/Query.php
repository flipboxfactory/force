<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services\resources;

use flipbox\ember\helpers\ObjectHelper;
use flipbox\force\criteria\QueryCriteria;
use flipbox\force\criteria\QueryCriteriaInterface;
use flipbox\force\transformers\collections\QueryTransformerCollection;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Query extends Component
{
    use traits\QueryTrait;

    /**
     * The resource name
     */
    const SALESFORCE_RESOURCE = 'soql';

    /**
     * @return array
     */
    public static function defaultTransformer()
    {
        return [
            'class' => QueryTransformerCollection::class
        ];
    }

    /*******************************************
     * CRITERIA
     *******************************************/

    /**
     * @param array $criteria
     * @return QueryCriteria
     */
    public function getCriteria(array $criteria = []): QueryCriteriaInterface
    {
        $object = new QueryCriteria();

        ObjectHelper::populate(
            $object,
            $criteria
        );

        return $object;
    }
}
