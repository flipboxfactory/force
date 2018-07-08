<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Dashboard;
use craft\services\Fields;
use craft\services\Plugins;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use flipbox\craft\psr3\Logger;
use flipbox\ember\helpers\UrlHelper;
use flipbox\ember\modules\LoggerTrait;
use flipbox\force\fields\Query as QueryField;
use flipbox\force\fields\Objects as SObjectIdsField;
use flipbox\force\models\Settings as SettingsModel;
use flipbox\force\patron\Events;
use flipbox\force\web\twig\variables\Force as ForceVariable;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method SettingsModel getSettings()
 *
 * @property services\Cache $cache
 * @property services\Connections $connections
 * @property Logger $psr3Logger
 * @property services\Resources $resources
 * @property services\ObjectAssociations $objectAssociations
 * @property services\ObjectsField $objectsField
 * @property services\Transformers $transformers
 */
class Force extends Plugin
{
    use LoggerTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Components
        $this->setComponents([
            'cache' => services\Cache::class,
            'connections' => services\Connections::class,
            'psr3Logger' => function () {
                return Craft::createObject([
                    'class' => Logger::class,
                    'logger' => static::getLogger(),
                    'category' => self::getLogFileName()
                ]);
            },
            'queryField' => services\QueryField::class,
            'queries' => services\Queries::class,
            'resources' => services\Resources::class,
            'objectAssociations' => services\ObjectAssociations::class,
            'objectsField' => services\ObjectsField::class,
            'transformers' => services\Transformers::class
        ]);

        // Modules
        $this->setModules([
            'cp' => cp\Cp::class

        ]);

        // Fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = SObjectIdsField::class;
                $event->types[] = QueryField::class;
            }
        );

        // Register our widgets
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = widgets\SObjectWidget::class;
            }
        );

        // Template variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('force', ForceVariable::class);
            }
        );

        // CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            [self::class, 'onRegisterCpUrlRules']
        );

        // Patron Access Token (if installed)
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_LOAD_PLUGINS,
            function () {
                if (Craft::$app->getPlugins()->getPlugin('patron')) {
                    Events::register();
                }
            }
        );
    }

    /**
     * @return string
     */
    protected static function getLogFileName(): string
    {
        return 'force';
    }

    /**
     * @inheritdoc
     */
    public function getCpNavItem()
    {
        return array_merge(
            parent::getCpNavItem(),
            [
                'subnav' => [
                    'force.queries' => [
                        'label' => Craft::t('force', 'Queries'),
                        'url' => 'force/queries'
                    ],
                    'force.data' => [
                        'label' => Craft::t('force', 'Data'),
                        'url' => 'force/data',
                    ],
                    'force.settings' => [
                        'label' => Craft::t('force', 'Settings'),
                        'url' => 'force/settings',
                    ]
                ]
            ]
        );
    }

    /*******************************************
     * SETTINGS
     *******************************************/

    /**
     * @inheritdoc
     * @return SettingsModel
     */
    public function createSettingsModel()
    {
        return new SettingsModel();
    }

    /**
     * @inheritdoc
     */
    public function getSettingsResponse()
    {
        Craft::$app->getResponse()->redirect(
            UrlHelper::cpUrl('force/settings')
        );

        Craft::$app->end();
    }

    /*******************************************
     * SERVICES
     *******************************************/

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return services\Cache
     */
    public function getCache()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('cache');
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return services\Connections
     */
    public function getConnections()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('connections');
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return Logger
     */
    public function getPsrLogger(): Logger
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('psr3Logger');
    }

    /**
     * @inheritdoc
     * @return services\QueryField
     */
    public function getQueryField()
    {
        return $this->get('queryField');
    }

    /**
     * @inheritdoc
     * @return services\Queries
     */
    public function getQueries()
    {
        return $this->get('queries');
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return services\ObjectAssociations
     */
    public function getObjectAssociations()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('objectAssociations');
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return services\ObjectsField
     */
    public function getObjectsField()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('objectsField');
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return services\Transformers
     */
    public function getTransformers()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('transformers');
    }

    /*******************************************
     * SUB-SERVICES
     *******************************************/

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return services\Resources
     */
    public function getResources()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('resources');
    }


    /*******************************************
     * MODULES
     *******************************************/

    /**
     * @return \flipbox\force\cp\Cp
     */
    public function getCp()
    {
        return $this->getModule('cp');
    }

    /*******************************************
     * EVENTS
     *******************************************/

    /**
     * @param RegisterUrlRulesEvent $event
     */
    public static function onRegisterCpUrlRules(RegisterUrlRulesEvent $event)
    {
        $event->rules = array_merge(
            $event->rules,
            [
                // ??
                'force' => 'force/cp/view/queries/index',

                // DATA
                'force/data' => 'force/cp/view/data/index',

                // QUERIES
                'force/queries' => 'force/cp/view/queries/index',
                'force/queries/<identifier:\d+>' => 'force/cp/view/queries/view',

                // SOBJECTS
                'force/sobjects' => 'force/cp/view/sobjects/index',
                'force/sobjects/<identifier:\d+>' => 'force/cp/view/sobjects/view',

                // SETTINGS
                'force/settings' => 'force/cp/settings/view/general/index',
                'force/settings/limits' => 'force/cp/settings/view/limits/index',

                // SETTINGS: QUERIES
                'force/settings/queries' => 'force/cp/settings/view/queries/index',
                'force/settings/queries/new' => 'force/cp/settings/view/queries/upsert',
                'force/settings/queries/<identifier:\d+>' => 'force/cp/settings/view/queries/upsert',

                // SETTINGS: SOBJECTS
                'force/settings/sobjects' => 'force/cp/settings/view/sobjects/index'
            ]
        );
    }
}
