<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\criteria;

use flipbox\force\Force;
use flipbox\force\queries\traits\QueryBuilderAttributeTrait;
use flipbox\force\transformers\collections\QueryTransformerCollection;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class QueryCriteria extends ResourceCriteria
{
    use QueryBuilderAttributeTrait;

    /**
     * @inheritdoc
     */
    protected $transformer = ['class' => QueryTransformerCollection::class];

    /**
     * @inheritdoc
     */
    public function fetch(array $config = [], $source = null)
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getQuery()->fetchFromCriteria($this)->execute($source);
    }
}
