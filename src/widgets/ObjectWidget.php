<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\widgets;

use Craft;
use craft\base\ElementInterface;
use craft\base\Widget;
use craft\db\Query;
use flipbox\craft\salesforce\fields\Objects;
use flipbox\craft\salesforce\web\assets\widgets\SyncWidget;

class ObjectWidget extends Widget
{
    /**
     * @var string
     */
    public $label = 'Sync Salesforce Object';

    /**
     * @var string
     */
    public $fieldId;

    /**
     * @var string
     */
    public $elementType;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('salesforce', 'Sync Salesforce Object');
    }

    /**
     * @inheritdoc
     */
    public static function iconPath()
    {
        $path = Craft::getAlias("@flipbox/force/icon-mask.svg");
        return is_string($path) ? $path : null;
    }

    /**
     * @inheritdoc
     */
    public static function maxColspan()
    {
        return 1;
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return $this->label;
    }

    /**
     * @inheritdoc
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate(
            'salesforce/_components/widgets/ObjectWidget/settings',
            [
                'widget' => $this,
                'fieldOptions' => $this->getFieldOptions(),
                'elementTypeOptions' => $this->getElementTypeOptions()
            ]
        );
    }

    /**
     * @inheritdoc
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getBodyHtml()
    {
        Craft::$app->getView()->registerAssetBundle(SyncWidget::class);

        return Craft::$app->getView()->renderTemplate(
            'salesforce/_components/widgets/ObjectWidget/body',
            [
                'widget' => $this
            ]
        );
    }

    /**
     * @return Objects[] The fields
     */
    public function getFields(): array
    {
        $results = $this->createFieldQuery()
            ->all();

        $fields = [];

        foreach ($results as $result) {
            $fields[] = Craft::$app->getFields()->createField($result);
        }

        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    ['fieldId', 'elementType'],
                    'required'
                ]
            ]
        );
    }

    /**
     * @return array
     */
    private function getFieldOptions()
    {
        $options = [];

        foreach ($this->getFields() as $field) {
            $options[] = [
                'label' => $field->name,
                'value' => $field->id
            ];
        }

        return $options;
    }

    /**
     * @return array
     */
    private function getElementTypeOptions()
    {
        $options = [];

        /** @var ElementInterface $elementType */
        foreach (Craft::$app->getElements()->getAllElementTypes() as $elementType) {
            $options[] = [
                'label' => $elementType::displayName(),
                'value' => $elementType
            ];
        }

        return $options;
    }

    /**
     * Returns a Query object prepped for retrieving fields.
     *
     * @return Query
     */
    private function createFieldQuery(): Query
    {
        return (new Query())
            ->select([
                'fields.id',
                'fields.dateCreated',
                'fields.dateUpdated',
                'fields.groupId',
                'fields.name',
                'fields.handle',
                'fields.context',
                'fields.instructions',
                'fields.translationMethod',
                'fields.translationKeyFormat',
                'fields.type',
                'fields.settings'
            ])
            ->from(['{{%fields}} fields'])
            ->orderBy(['fields.name' => SORT_ASC])
            ->andWhere([
                'fields.type' => Objects::class
            ]);
    }
}
