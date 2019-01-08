<?php

namespace flipbox\force\queries;

use craft\db\Query;
use flipbox\craft\ember\queries\AuditAttributesTrait;
use flipbox\craft\ember\queries\PopulateObjectTrait;
use Flipbox\Salesforce\Query\QueryBuilderInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SOQLQuery extends Query
{
    use PopulateObjectTrait,
        AuditAttributesTrait;

    /*******************************************
     * RESULTS
     *******************************************/

    /**
     * @inheritdoc
     * @return QueryBuilderInterface
     */
    public function one($db = null)
    {
        if (null === ($config = parent::one($db))) {
            return null;
        }

        return $this->createObject($config);
    }

    /**
     * @param array $config
     * @return QueryBuilderInterface
     */
    protected function createObject(array $config)
    {

    }


    /*******************************************
     * PREPARE
     *******************************************/

    /**
     * @inheritdoc
     */
    public function prepare($builder)
    {
        $this->applyAuditAttributeConditions();

        return parent::prepare($builder);
    }
}
