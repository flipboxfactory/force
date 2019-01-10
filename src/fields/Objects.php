<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\fields;

use Craft;
use flipbox\craft\integration\fields\Integrations;
use flipbox\craft\salesforce\fields\actions\SyncItemFrom;
use flipbox\craft\salesforce\fields\actions\SyncItemTo;
use flipbox\craft\salesforce\fields\actions\SyncTo;
use flipbox\craft\salesforce\Force;
use flipbox\craft\salesforce\models\Settings;
use flipbox\craft\salesforce\records\Connection;
use flipbox\craft\salesforce\records\ObjectAssociation;
use Flipbox\Salesforce\Connections\ConnectionInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Objects extends Integrations
{
    /**
     * The Plugin's translation category
     */
    const TRANSLATION_CATEGORY = 'salesforce';

    /**
     * @inheritdoc
     */
    const INPUT_TEMPLATE_PATH = 'salesforce/_components/fieldtypes/Objects/input';

    /**
     * @inheritdoc
     */
    const INPUT_ITEM_TEMPLATE_PATH = 'salesforce/_components/fieldtypes/Objects/_inputItem';

    /**
     * @inheritdoc
     */
    const SETTINGS_TEMPLATE_PATH = 'salesforce/_components/fieldtypes/Objects/settings';

    /**
     * @inheritdoc
     */
    const ACTION_PREFORM_ACTION_PATH = 'salesforce/cp/fields/perform-action';

    /**
     * @inheritdoc
     */
    const ACTION_CREATE_ITEM_PATH = 'salesforce/cp/fields/create-item';

    /**
     * @inheritdoc
     */
    const ACTION_ASSOCIATION_ITEM_PATH = 'salesforce/cp/objects/associate';

    /**
     * @inheritdoc
     */
    const ACTION_DISSOCIATION_ITEM_PATH = 'salesforce/cp/objects/dissociate';

    /**
     * @inheritdoc
     */
    const ACTION_PREFORM_ITEM_ACTION_PATH = 'salesforce/cp/fields/perform-item-action';

    /**
     * @inheritdoc
     */
    protected $defaultAvailableActions = [
        SyncTo::class
    ];

    /**
     * @inheritdoc
     */
    protected $defaultAvailableItemActions = [
        SyncItemFrom::class,
        SyncItemTo::class,
    ];

    /**
     * @inheritdoc
     */
    public static function recordClass(): string
    {
        return ObjectAssociation::class;
    }

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('salesforce', 'Salesforce Objects');
    }

    /**
     * @inheritdoc
     */
    public static function defaultSelectionLabel(): string
    {
        return Craft::t('salesforce', 'Add a Salesforce Object');
    }

    /*******************************************
     * CONNECTION
     *******************************************/

    /**
     * @return ConnectionInterface
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     */
    public function getConnection(): ConnectionInterface
    {
        return Connection::getOne(Settings::DEFAULT_CONNECTION);
    }

    /*******************************************
     * CACHE
     *******************************************/

    /**
     * @return CacheInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function getCache(): CacheInterface
    {
        $service = Force::getInstance()->getCache();
        return $service->get($service::DEFAULT_CACHE);
    }
}
