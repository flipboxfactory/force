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
use flipbox\ember\validators\LimitValidator;
use flipbox\force\db\SObjectAssociationQuery;
use flipbox\force\fields\SObjects;
use flipbox\force\Force;
use flipbox\force\migrations\SObjectAssociations;
use flipbox\force\validators\AssociationFieldLimitValidator;
use Psr\Log\InvalidArgumentException;

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
        if (!in_array(self::tableAlias(), Craft::$app->getDb()->getSchema()->tableNames, true)) {
            $this->createTable();
        }

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
                ],[
                    [
                        'fieldId'
                    ],
                    LimitValidator::class,
                    'query' => function(SObjectAssociation $model) {
                        return $model::find()
                            ->field($model->fieldId)
                            ->element($model->elementId)
                            ->site($model->siteId)
                            ->andWhere([
                                '!=',
                                static::TARGET_ATTRIBUTE,
                                $model->{static::TARGET_ATTRIBUTE}
                            ]);
                    },
                    'limit' => function(SObjectAssociation $model) {
                        return $this->getFieldLimit($model->fieldId);
                    },
                    'message' => "Limit exceeded."
                ],[
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

    /**
     * @param int $fieldId
     * @return SObjects
     */
    protected function resolveField(int $fieldId): SObjects
    {
        $field = Craft::$app->getFields()->getFieldById($fieldId);

        if ($field === null || !$field instanceof SObjects) {
            throw new InvalidArgumentException(sprintf(
                "Field must be an instance of '%s', '%s' given.",
                SObjects::class,
                $field ? get_class($field) : 'FIELD NOT FOUND'
            ));
        }

        return $field;
    }

    /**
     * @param int $fieldId
     * @return int
     */
    protected function getFieldLimit(int $fieldId): int
    {
        return (int) $this->resolveField($fieldId)->limit;
    }

    /**
     * @return bool
     * @throws \Throwable
     */
    private function createTable(): bool
    {
        ob_start();
        (new SObjectAssociations())->up();
        ob_end_clean();

        return true;
    }
}
