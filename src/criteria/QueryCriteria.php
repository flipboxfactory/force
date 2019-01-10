<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\criteria;

use Flipbox\Salesforce\Query\QueryBuilderAttributeTrait;
use Flipbox\Salesforce\Resources\Query;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class QueryCriteria extends AbstractCriteria implements QueryCriteriaInterface
{
    use ConnectionTrait,
        CacheTrait,
        LoggerTrait,
        QueryBuilderAttributeTrait;

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

        return Query::query(
            $this->getConnection(),
            $this->getCache(),
            $this->getQuery()->build(),
            $this->getLogger(),
            $config
        );
    }
}
