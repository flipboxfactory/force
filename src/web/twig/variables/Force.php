<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\web\twig\variables;

use flipbox\force\criteria\ObjectAccessorCriteriaInterface;
use flipbox\force\Force as ForcePlugin;
use flipbox\force\models\Settings;
use flipbox\force\services\Cache;
use flipbox\force\services\Connections;
use flipbox\force\services\Resources;
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
            'resources' => ForcePlugin::getInstance()->getResources(),
            'connections' => ForcePlugin::getInstance()->getConnections(),
            'cache' => ForcePlugin::getInstance()->getCache()
        ]);
    }

    /**
     * @param array $criteria
     * @return \flipbox\force\criteria\QueryCriteria
     */
    public function getQuery(array $criteria = [])
    {
        if (is_string($criteria)) {
            return ForcePlugin::getInstance()->getQueries()->get($criteria);
        }

        return $this->getResources()->getQuery()->getCriteria($criteria);
    }

    /**
     * @param array $criteria
     * @return ObjectAccessorCriteriaInterface
     */
    public function getObject(array $criteria = []): ObjectAccessorCriteriaInterface
    {
        return $this->getResources()->getSObject()->getAccessorCriteria($criteria);
    }

    /**
     * Sub-Variables that are accessed 'craft.force.settings'
     *
     * @return Settings
     */
    public function getSettings()
    {
        return ForcePlugin::getInstance()->getSettings();
    }

    /**
     * @inheritdoc
     * @return Resources
     */
    public function getResources()
    {
        return $this->get('resources');
    }

    /**
     * @inheritdoc
     * @return Connections
     */
    public function getConnections()
    {
        return $this->get('connections');
    }

    /**
     * @inheritdoc
     * @return Cache
     */
    public function getCache()
    {
        return $this->get('cache');
    }
}
