<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services;

use flipbox\force\Force;
use flipbox\force\models\Settings as SettingsModel;
use Flipbox\Salesforce\Salesforce;
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

        Salesforce::setLogger(
            Force::getInstance()->getLogger()
        );

        $this->setComponents([
            'general' => resources\General::class,
            'query' => resources\Query::class,
            'sObject' => resources\SObject::class
        ]);
    }

    /**
     * @inheritdoc
     * @return resources\Query
     */
    public function getQuery()
    {
        return $this->get('query');
    }

    /**
     * @inheritdoc
     * @return resources\SObject
     */
    public function getSObject()
    {
        return $this->get('sObject');
    }

    /**
     * @inheritdoc
     * @return resources\General
     */
    public function getGeneral()
    {
        return $this->get('general');
    }
}
