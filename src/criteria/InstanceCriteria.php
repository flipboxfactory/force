<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\criteria;

use Flipbox\Salesforce\Resources\Instance;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class InstanceCriteria extends AbstractCriteria implements InstanceCriteriaInterface
{
    use ConnectionTrait,
        CacheTrait,
        LoggerTrait;

    /**
     * @param array $criteria
     * @param array $config
     * @return ResponseInterface
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function describe(array $criteria = [], array $config = []): ResponseInterface
    {
        $this->populate($criteria);

        return Instance::describe(
            $this->getConnection(),
            $this->getCache(),
            $this->getLogger(),
            $config
        );
    }

    /**
     * @param array $criteria
     * @param array $config
     * @return ResponseInterface
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function limits(array $criteria = [], array $config = []): ResponseInterface
    {
        $this->populate($criteria);

        return Instance::limits(
            $this->getConnection(),
            $this->getCache(),
            $this->getLogger(),
            $config
        );
    }

    /**
     * @param array $criteria
     * @param array $config
     * @return ResponseInterface
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function resources(array $criteria = [], array $config = []): ResponseInterface
    {
        $this->populate($criteria);

        return Instance::resources(
            $this->getConnection(),
            $this->getCache(),
            $this->getLogger(),
            $config
        );
    }
}
