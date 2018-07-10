<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\criteria;

use flipbox\ember\helpers\ObjectHelper;
use flipbox\force\Force;
use flipbox\force\query\traits\QueryBuilderAttributeTrait;
use flipbox\force\services\resources\Query;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class QueryCriteria extends BaseObject implements QueryCriteriaInterface
{
    use traits\ConnectionTrait,
        traits\CacheTrait,
        traits\TransformerCollectionTrait,
        QueryBuilderAttributeTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->transformer = Query::defaultTransformer();
        parent::init();
    }

    /**
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function fetch(array $config = [], $source = null)
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getQuery()->query($this, $source);
    }

    /**
     * @inheritdoc
     */
    protected function prepare(array $criteria = [])
    {
        ObjectHelper::populate(
            $this,
            $criteria
        );
    }
}
