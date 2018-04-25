<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers\collections;

use Flipbox\Salesforce\Transformers\Response\QueryCollection;

class QueryTransformerCollection extends DynamicTransformerCollection
{
    /**
     * @inheritdoc
     */
    public function getTransformer(string $key)
    {
        return new QueryCollection([
            'record' => parent::getTransformer($key)
        ]);
    }
}
