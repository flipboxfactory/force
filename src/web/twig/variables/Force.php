<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\web\twig\variables;

use flipbox\craft\ember\helpers\QueryHelper;
use flipbox\craft\integration\queries\IntegrationConnectionQuery;
use flipbox\craft\salesforce\Force as ForcePlugin;
use flipbox\craft\salesforce\models\Settings;
use flipbox\craft\salesforce\queries\SOQLQuery;
use flipbox\craft\salesforce\records\Connection;
use flipbox\craft\salesforce\records\SOQL;
use flipbox\craft\salesforce\services\Cache;
use flipbox\craft\salesforce\criteria\ObjectAccessorCriteria;
use flipbox\craft\salesforce\criteria\ObjectAccessorCriteriaInterface;
use yii\di\ServiceLocator;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Force extends ServiceLocator
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setComponents([
            'cache' => ForcePlugin::getInstance()->getCache()
        ]);
    }

    /**
     * @param array $criteria
     * @return SOQLQuery
     */
    public function getQuery(array $criteria = []): SOQLQuery
    {
        if (is_string($criteria)) {
            $criteria = [(is_numeric($criteria) ? 'id' : 'handle') => $criteria];
        }

        $query = SOQL::find();

        QueryHelper::configure(
            $query,
            $criteria
        );

        return $query;
    }

    /**
     * @param array $criteria
     * @return ObjectAccessorCriteriaInterface
     */
    public function getObject(array $criteria = []): ObjectAccessorCriteriaInterface
    {
        return new ObjectAccessorCriteria($criteria);
    }

    /**
     * Sub-Variables that are accessed 'craft.salesforce.settings'
     *
     * @return Settings
     */
    public function getSettings()
    {
        return ForcePlugin::getInstance()->getSettings();
    }


    /**
     * @param array $config
     * @return IntegrationConnectionQuery
     */
    public function getConnections(array $config = []): IntegrationConnectionQuery
    {
        $query = Connection::find();

        QueryHelper::configure(
            $query,
            $config
        );

        return $query;
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return Cache
     */
    public function getCache(): Cache
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('cache');
    }
}
