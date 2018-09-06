<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\criteria;

use flipbox\ember\helpers\ObjectHelper;
use flipbox\force\Force;
use flipbox\force\search\traits\SearchBuilderAttributeTrait;
use flipbox\force\services\resources\Search;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SearchCriteria extends BaseObject implements SearchCriteriaInterface
{
    use traits\ConnectionTrait,
        traits\CacheTrait,
        traits\TransformerCollectionTrait,
        SearchBuilderAttributeTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->transformer = Search::defaultTransformer();
        parent::init();
    }

    /**
     * @param array $config
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function fetch(array $config = [], array $extra = [])
    {
        $this->prepare($config);
        return Force::getInstance()->getResources()->getSearch()->search($this, $extra);
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
