<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\elements\db\ElementQueryInterface;
use flipbox\ember\helpers\ModelHelper;
use flipbox\ember\validators\MinMaxValidator;
use flipbox\force\connections\ConnectionInterface;
use flipbox\force\db\ObjectAssociationQuery;
use flipbox\force\Force;
use flipbox\force\records\ObjectAssociation;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Objects extends Field
{
    /**
     * The action event name
     */
    const EVENT_REGISTER_ACTIONS = 'registerActions';

    /**
     * The action event name
     */
    const EVENT_REGISTER_AVAILABLE_ACTIONS = 'registerAvailableActions';

    /**
     * The item action event name
     */
    const EVENT_REGISTER_ITEM_ACTIONS = 'registerItemActions';

    /**
     * The item action event name
     */
    const EVENT_REGISTER_AVAILABLE_ITEM_ACTIONS = 'registerAvailableItemActions';

    /**
     * The input template path
     */
    const INPUT_TEMPLATE_PATH = 'force/_components/fieldtypes/Objects/input';

    /**
     * @var string
     */
    public $object;

    /**
     * @var int|null
     */
    public $min;

    /**
     * @var int|null
     */
    public $max;

    /**
     * @var string
     */
    public $viewUrl = '';

    /**
     * @var string
     */
    public $listUrl = '';

    /**
     * @var array
     */
    public $selectedActions = [];

    /**
     * @var array
     */
    public $selectedItemActions = [];

    /**
     * @var string|null
     */
    public $selectionLabel;

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

    /**
     * @inheritdoc
     */
    public static function hasContentColumn(): bool
    {
        return false;
    }

    /*******************************************
     * VALIDATION
     *******************************************/

    /**
     * @inheritdoc
     */
    public function getElementValidationRules(): array
    {
        return [
            [
                MinMaxValidator::class,
                'min' => $this->min ? (int)$this->min : null,
                'max' => $this->max ? (int)$this->max : null,
                'tooFew' => Craft::t(
                    'force',
                    '{attribute} should contain at least {min, number} {min, plural, one{domain} other{domains}}.'
                ),
                'tooMany' => Craft::t(
                    'force',
                    '{attribute} should contain at most {max, number} {max, plural, one{domain} other{domains}}.'
                ),
                'skipOnEmpty' => false
            ]
        ];
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

    /*******************************************
     * VALUE
     *******************************************/

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        return Force::getInstance()->getObjectsField()->normalizeValue(
            $this,
            $value,
            $element
        );
    }


    /*******************************************
     * ELEMENT
     *******************************************/

    /**
     * @inheritdoc
     */
    public function modifyElementsQuery(ElementQueryInterface $query, $value)
    {
        return Force::getInstance()->getObjectsField()->modifyElementsQuery(
            $this,
            $query,
            $value
        );
    }


    /*******************************************
     * RULES
     *******************************************/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    'object',
                    'required',
                    'message' => Craft::t('force', 'Salesforce Object cannot be empty.')
                ],
                [
                    [
                        'object',
                        'min',
                        'max',
                        'viewUrl',
                        'listUrl',
                        'selectionLabel'
                    ],
                    'safe',
                    'on' => [
                        ModelHelper::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );
    }

    /*******************************************
     * SEARCH
     *******************************************/

    /**
     * @param ObjectAssociationQuery $value
     * @inheritdoc
     */
    public function getSearchKeywords($value, ElementInterface $element): string
    {
        $objects = [];

        /** @var ObjectAssociation $association */
        foreach ($value->all() as $association) {
            array_push($objects, $association->objectId);
        }

        return parent::getSearchKeywords($objects, $element);
    }

    /*******************************************
     * VIEWS
     *******************************************/

    /**
     * @inheritdoc
     * @param ObjectAssociationQuery $value
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $value->limit(null);
        return Force::getInstance()->getObjectsField()->getInputHtml($this, $value, $element, false);
    }

    /**
     * @inheritdoc
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function getSettingsHtml()
    {
        return Force::getInstance()->getObjectsField()->getSettingsHtml($this);
    }


    /*******************************************
     * EVENTS
     *******************************************/

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function afterElementSave(ElementInterface $element, bool $isNew)
    {
        Force::getInstance()->getObjectAssociations()->save(
            $element->getFieldValue($this->handle)
        );

        parent::afterElementSave($element, $isNew);
    }
}