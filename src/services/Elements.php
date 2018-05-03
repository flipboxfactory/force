<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\services;

use craft\base\Element;
use craft\base\ElementInterface;
use flipbox\ember\helpers\ObjectHelper;
use flipbox\force\criteria\SObjectCriteria;
use flipbox\force\fields\SObjects;
use flipbox\force\Force;
use flipbox\force\helpers\TransformerHelper;
use flipbox\force\pipeline\stages\ElementAssociationStage;
use flipbox\force\pipeline\stages\ElementSaveStage;
use Flipbox\Salesforce\Resources\SObject\Row\Get as SObjectRowGet;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Elements extends Component
{
    /**
     * @param ElementInterface $element
     * @param SObjects $field
     * @param null $criteria
     * @return false|string
     * @throws \yii\base\InvalidConfigException
     */
    public function syncDown(
        ElementInterface $element,
        SObjects $field,
        $criteria = null
    ): bool {
        /** @var Element $element */

        $this->rowBuilder(
            $element,
            $field,
            $criteria
        )->build()->pipe(
            new ElementSaveStage($field)
        )->pipe(
            new ElementAssociationStage($field)
        )->process(null, $element);

        return !$element->hasErrors();
    }

    /**
     * @param ElementInterface $element
     * @param SObjects $field
     * @param null $criteria
     * @return false|string
     * @throws \yii\base\InvalidConfigException
     */
    public function syncUp(
        ElementInterface $element,
        SObjects $field,
        $criteria = null
    ): bool {
        /** @var Element $element */
        $criteria = $this->resolveCriteria($criteria);

        $criteria->sObject = $field->sObject;
        if ($criteria->id === null) {
            $criteria->id = $element;
        }

        if (empty($criteria->payload)) {
            $criteria->payload = $element;
        }

        Force::getInstance()->getResources()->getSObject()->upsertRow(
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->sObject,
            $criteria->payload,
            $criteria->id,
            TransformerHelper::populateTransformerCollection(
                $criteria->getTransformer(),
                [
                    'resource' => [get_class($element)]
                ]
            )
        )->build()->pipe(
            new ElementAssociationStage($field)
        )->process(null, $element);

        return !$element->hasErrors();
    }


    /*******************************************
     * ROW
     *******************************************/

    /**
     * @param ElementInterface $element
     * @param SObjects $field
     * @param null $criteria
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function getRow(
        ElementInterface $element,
        SObjects $field,
        $criteria = null
    ): bool {
        /** @var Element $element */

        $this->rowBuilder(
            $element,
            $field,
            $criteria
        )->execute($element);

        return !$element->hasErrors();
    }

    /**
     * @param ElementInterface $element
     * @param SObjects $field
     * @param null $criteria
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function upsertRow(
        ElementInterface $element,
        SObjects $field,
        $criteria = null
    ): bool {
        /** @var Element $element */
        $criteria = $this->resolveCriteria($criteria);

        $criteria->sObject = $field->sObject;
        if (empty($criteria->id)) {
            $criteria->id = $element;
        }

        if (empty($criteria->payload)) {
            $criteria->payload = $element;
        }

        Force::getInstance()->getResources()->getSObject()->upsertRow(
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->sObject,
            $criteria->payload,
            $criteria->id,
            TransformerHelper::populateTransformerCollection(
                $criteria->getTransformer(),
                [
                    'resource' => [get_class($element)]
                ]
            )
        )->execute($element);

        return !$element->hasErrors();
    }

    /**
     * @param ElementInterface $element
     * @param SObjects $field
     * @param null $criteria
     * @return SObjectRowGet
     * @throws \yii\base\InvalidConfigException
     */
    private function rowBuilder(
        ElementInterface $element,
        SObjects $field,
        $criteria = null
    ) {
        /** @var Element $element */
        $criteria = $this->resolveCriteria($criteria);

        if (empty($criteria->sObject)) {
            $criteria->sObject = $field->sObject;
        }

        if (empty($criteria->id)) {
            $criteria->id = $element;
        }

        TransformerHelper::populateTransformerCollection(
            $criteria->getTransformer(),
            [
                'resource' => [get_class($element)]
            ]
        );

        return Force::getInstance()->getResources()->getSObject()->getRowFromCriteria(
            $criteria
        );
    }

    /**
     * @param null $criteria
     * @return SObjectCriteria
     * @throws \yii\base\InvalidConfigException
     */
    private function resolveCriteria($criteria = null): SObjectCriteria
    {
        if ($criteria instanceof SObjectCriteria) {
            return $criteria;
        }

        if ($criteria === null) {
            return new SObjectCriteria();
        }

        if (!is_array($criteria)) {
            $criteria = ['class' => $criteria];
        }

        $criteria['class'] = $criteria['class'] ?? SObjectCriteria::class;

        return ObjectHelper::create($criteria);
    }
}
