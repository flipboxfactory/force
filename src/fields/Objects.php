<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\fields;

use Craft;
use flipbox\craft\integration\fields\Integrations;
use flipbox\craft\integration\services\IntegrationAssociations;
use flipbox\craft\integration\services\IntegrationField;
use flipbox\force\connections\ConnectionInterface;
use flipbox\force\Force;
use flipbox\force\services\ObjectAssociations;
use flipbox\force\services\ObjectsField;
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
    const TRANSLATION_CATEGORY = 'force';

    /**
     * @inheritdoc
     */
    const INPUT_TEMPLATE_PATH = 'force/_components/fieldtypes/Objects/input';

    /**
     * @inheritdoc
     */
    const INPUT_ITEM_TEMPLATE_PATH = 'force/_components/fieldtypes/Objects/_inputItem';

    /**
     * @inheritdoc
     */
    const SETTINGS_TEMPLATE_PATH = 'force/_components/fieldtypes/Objects/settings';

    /**
     * @inheritdoc
     */
    const ACTION_PREFORM_ACTION_PATH = 'force/cp/fields/perform-action';

    /**
     * @inheritdoc
     */
    const ACTION_CREATE_ITEM_PATH = 'force/cp/fields/create-item';

    /**
     * @inheritdoc
     */
    const ACTION_ASSOCIATION_ITEM_PATH = 'force/cp/objects/associate';

    /**
     * @inheritdoc
     */
    const ACTION_DISSOCIATION_ITEM_PATH = 'force/cp/objects/dissociate';

    /**
     * @inheritdoc
     */
    const ACTION_PREFORM_ITEM_ACTION_PATH = 'force/cp/fields/perform-item-action';

    /**
     * @inheritdoc
     * @return ObjectsField
     */
    protected function fieldService(): IntegrationField
    {
        return Force::getInstance()->getObjectsField();
    }

    /**
     * @inheritdoc
     * @return ObjectAssociations
     */
    protected function associationService(): IntegrationAssociations
    {
        return Force::getInstance()->getObjectAssociations();
    }

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('force', 'Salesforce Objects');
    }

    /**
     * @inheritdoc
     */
    public static function defaultSelectionLabel(): string
    {
        return Craft::t('force', 'Add a Salesforce Object');
    }

    /*******************************************
     * CONNECTION
     *******************************************/

    /**
     * @return ConnectionInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function getConnection(): ConnectionInterface
    {
        $service = Force::getInstance()->getConnections();
        return $service->get($service::DEFAULT_CONNECTION);
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
