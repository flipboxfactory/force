<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services;

use flipbox\force\models\Settings as SettingsModel;
use yii\di\ServiceLocator;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method SettingsModel getSettings()
 */
class Resources extends ServiceLocator
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setComponents([
            resources\Instance::SALESFORCE_RESOURCE => resources\Instance::class,
            resources\Query::SALESFORCE_RESOURCE => resources\Query::class,
            resources\Object::SALESFORCE_RESOURCE => resources\Object::class,
            resources\Search::SALESFORCE_RESOURCE => resources\Search::class
        ]);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return resources\Object
     */
    public function getObject(): resources\Object
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get(resources\Object::SALESFORCE_RESOURCE);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return resources\Instance
     */
    public function getGeneral(): resources\Instance
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get(resources\Instance::SALESFORCE_RESOURCE);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return resources\Query
     */
    public function getQuery(): resources\Query
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get(resources\Query::SALESFORCE_RESOURCE);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return resources\Search
     */
    public function getSearch(): resources\Search
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get(resources\Search::SALESFORCE_RESOURCE);
    }
}
