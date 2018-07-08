<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services\resources;

use flipbox\ember\helpers\ObjectHelper;
use flipbox\force\criteria\SearchCriteria;
use flipbox\force\criteria\SearchCriteriaInterface;
use flipbox\force\transformers\collections\SearchTransformerCollection;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Search extends Component
{
    use traits\SearchTrait;

    /**
     * The resource name
     */
    const SALESFORCE_RESOURCE = 'search';

    /**
     * @return array
     */
    public static function defaultTransformer()
    {
        return [
            'class' => SearchTransformerCollection::class
        ];
    }

    /*******************************************
     * CRITERIA
     *******************************************/

    /**
     * @param array $criteria
     * @return SearchCriteria
     */
    public function getCriteria(array $criteria = []): SearchCriteriaInterface
    {
        $object = new SearchCriteria();

        ObjectHelper::populate(
            $object,
            $criteria
        );

        return $object;
    }
}
