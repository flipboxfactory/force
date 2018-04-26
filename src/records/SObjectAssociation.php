<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\records;

use Craft;
use flipbox\craft\sortable\associations\records\SortableAssociation;
use flipbox\craft\sortable\associations\services\SortableAssociations;
use flipbox\ember\helpers\ModelHelper;
use flipbox\ember\records\traits\ElementAttribute;
use flipbox\ember\records\traits\SiteAttribute;
use flipbox\force\db\SObjectAssociationQuery;
use flipbox\force\Force;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property int $fieldId
 * @property string $sObjectId
 */
class SObjectAssociation extends SortableAssociation
{
    use SiteAttribute,
        ElementAttribute;

    /**
     * @inheritdoc
     */
    const TABLE_ALIAS = 'salesforce_sobject_associations';

    /**
     * @inheritdoc
     */
    const TARGET_ATTRIBUTE = 'sObjectId';

    /**
     * @inheritdoc
     */
    const SOURCE_ATTRIBUTE = 'elementId';

    /**
     * @inheritdoc
     */
    public function __construct(array $config = [])
    {
        Force::getInstance()->getSObjectAssociations()->ensureTableExists();
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableAlias()
    {
        return parent::tableAlias() . Force::getInstance()->getSettings()->sObjectAssociationTablePostfix;
    }

    /**
     * @inheritdoc
     * @return SObjectAssociationQuery
     */
    public static function find()
    {
        return Craft::createObject(SObjectAssociationQuery::class, [get_called_class()]);
    }

    /**
     * @return SortableAssociations
     */
    protected function associationService(): SortableAssociations
    {
        return Force::getInstance()->getSObjectAssociations();
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            $this->siteRules(),
            $this->elementRules(),
            [
                [
                    [
                        'fieldId'
                    ],
                    'number',
                    'integerOnly' => true
                ],
                [
                    [
                        'fieldId',
                        'sObjectId'
                    ],
                    'safe',
                    'on' => [
                        ModelHelper::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );
    }
}
