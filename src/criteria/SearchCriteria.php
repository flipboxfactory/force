<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\criteria;

use Flipbox\Salesforce\Resources\Search;
use Flipbox\Salesforce\Search\SearchBuilderAttributeTrait;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SearchCriteria extends AbstractCriteria implements SearchCriteriaInterface
{
    use ConnectionTrait,
        CacheTrait,
        LoggerTrait,
        SearchBuilderAttributeTrait;

    /**
     * @param array $criteria
     * @param array $config
     * @return ResponseInterface
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function fetch(array $criteria = [], array $config = []): ResponseInterface
    {
        $this->populate($criteria);

        return Search::search(
            $this->getConnection(),
            $this->getCache(),
            $this->getSearch()->build(),
            $this->getLogger(),
            $config
        );
    }
}
