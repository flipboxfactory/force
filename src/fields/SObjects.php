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
use flipbox\force\criteria\SObjectCriteria;
use flipbox\force\db\SObjectAssociationQuery;
use flipbox\force\Force;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SObjects extends Field
{
    /**
     * The action event name
     */
    const EVENT_REGISTER_ACTIONS = 'registerActions';

    /**
     * The row action event name
     */
    const EVENT_REGISTER_ROW_ACTIONS = 'registerRowActions';

    /**
     * @var string
     */
    public $sObject;

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
                'min' => $this->min,
                'max' => $this->max,
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
     * VALUE
     *******************************************/

    /**
     * @param array $criteria
     * @return SObjectCriteria
     */
    public function createCriteria(array $criteria = [])
    {
        return Force::getInstance()->getResources()->getSObject()->getCriteria(array_merge(
            $criteria,
            [
                'sObject' => $this->sObject
            ]
        ));
    }

    /*******************************************
     * VALUE
     *******************************************/

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        return Force::getInstance()->getSObjectsField()->normalizeValue(
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
        return Force::getInstance()->getSObjectsField()->modifyElementsQuery(
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
                    'sObject',
                    'required',
                    'message' => Craft::t('force', 'Salesforce Object cannot be empty.')
                ],
                [
                    [
                        'sObject',
                        'limit',
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
     * @param SObjectAssociationQuery $value
     * @inheritdoc
     */
    public function getSearchKeywords($value, ElementInterface $element): string
    {
        $sobjects = [];

        /** @var SObjectCriteria $association */
        foreach ($value->all() as $association) {
            array_push($sobjects, $association->id);
        }

        return parent::getSearchKeywords($sobjects, $element);
    }

    /*******************************************
     * VIEWS
     *******************************************/

    /**
     * @param SObjectAssociationQuery $value
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        return Force::getInstance()->getSObjectsField()->getInputHtml($this, $value, $element, false);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Force::getInstance()->getSObjectsField()->getSettingsHtml($this);
    }


    /*******************************************
     * EVENTS
     *******************************************/

    /**
     * @inheritdoc
     */
    public function afterElementSave(ElementInterface $element, bool $isNew)
    {
        Force::getInstance()->getSObjectAssociations()->save(
            $element->getFieldValue($this->handle)
        );

        parent::afterElementSave($element, $isNew);
    }
}
