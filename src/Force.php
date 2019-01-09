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
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Dashboard;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use craft\web\View;
use flipbox\craft\ember\helpers\UrlHelper;
use flipbox\craft\ember\modules\LoggerTrait;
use flipbox\craft\psr3\Logger;
use flipbox\force\fields\Objects as ObjectsField;
use flipbox\force\models\Settings as SettingsModel;
use flipbox\force\web\twig\variables\Force as ForceVariable;
use Flipbox\Salesforce\Salesforce;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method SettingsModel getSettings()
 *
 * @property services\Cache $cache
 * @property Logger $psr3Logger
 */
class Force extends Plugin
{
    use LoggerTrait;

    /**
     * @inheritdocfind
     */
    public function init()
    {
        parent::init();

        // Components
        $this->setComponents([
            'cache' => services\Cache::class,
            'psr3Logger' => function () {
                return Craft::createObject([
                    'class' => Logger::class,
                    'logger' => static::getLogger(),
                    'category' => self::getLogFileName()
                ]);
            }
        ]);

        // Pass logger along to package
        Salesforce::setLogger(
            static::getPsrLogger()
        );

        // Modules
        $this->setModules([
            'cp' => cp\Cp::class

        ]);

        // Fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = ObjectsField::class;
            }
        );

        // Register our widgets
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = widgets\ObjectWidget::class;
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

        // Integration template directory
        Event::on(
            View::class,
            View::EVENT_REGISTER_CP_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $e) {
                $e->roots['flipbox/integration'] = Craft::$app->getPath()->getVendorPath() .
                    '/flipboxfactory/craft-integration/src/templates';
            }
        );

        // CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            [self::class, 'onRegisterCpUrlRules']
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
     * @throws \yii\base\ExitException
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
    public function getCache(): services\Cache
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('cache');
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


    /*******************************************
     * MODULES
     *******************************************/

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return cp\Cp
     */
    public function getCp(): cp\Cp
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
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

                // QUERIES
                'force/queries' => 'force/cp/view/queries/index',
                'force/queries/new' => 'force/cp/view/queries/upsert',
                'force/queries/<identifier:\d+>' => 'force/cp/view/queries/upsert',

//                // SOBJECTS
//                'force/objects' => 'force/cp/view/objects/index',
//                'force/objects/<identifier:\d+>' => 'force/cp/view/objects/view',

                // SETTINGS
                'force/settings' => 'force/cp/settings/view/general/index',
                'force/settings/limits' => 'force/cp/settings/view/limits/index',

                // SETTINGS: CONNECTIONS
                'force/settings/connections' => 'force/cp/settings/view/connections/index',
                'force/settings/connections/new' => 'force/cp/settings/view/connections/upsert',
                'force/settings/connections/<identifier:\d+>' => 'force/cp/settings/view/connections/upsert',

                // SETTINGS: SOBJECTS
                'force/settings/objects' => 'force/cp/settings/view/objects/index'
            ]
        );
    }
}
