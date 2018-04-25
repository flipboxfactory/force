<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\controllers\settings\view;

use Craft;
use flipbox\ember\helpers\ArrayHelper;
use flipbox\force\Force;
use flipbox\force\transformers\collections\AdminTransformerCollection;
use yii\web\Response;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SobjectsController extends AbstractController
{
    /**
     * The template base path
     */
    const TEMPLATE_BASE = parent::TEMPLATE_BASE . DIRECTORY_SEPARATOR . 'sobjects';

    /**
     * The index view template path
     */
    const TEMPLATE_INDEX = self::TEMPLATE_BASE . DIRECTORY_SEPARATOR . 'index';

    /**
     * @return Response
     */
    public function actionIndex(): Response
    {
        $variables = [];
        $this->baseVariables($variables);

        $sobject = Craft::$app->getRequest()->getParam('sObject');
        $describedSobject = null;

        if ($sobject !== null) {
            $describedSobject = Force::getInstance()->getResources()->getSObject()->getCriteria([
                'sObject' => $sobject,
                'transformer' => AdminTransformerCollection::class
            ])->describe();
        }

        $variables['describedSobject'] = $describedSobject;
        $variables['sObjectOptions'] = $this->getSObjectOptions();
        $variables['tabs'] = $this->getTabs();

        return $this->renderTemplate(
            static::TEMPLATE_INDEX,
            $variables
        );
    }

    /**
     * Gets a formatted array of available Salesforce Objects.
     *
     * @return array
     */
    private function getSObjectOptions()
    {
        $describe = Force::getInstance()->getResources()->getGeneral()->getCriteria([
            'transformer' => AdminTransformerCollection::class
        ])->describe();
        $describeOptions = [];

        foreach (ArrayHelper::getValue($describe, 'sobjects', []) as $sobject) {
            $describeOptions[] = [
                'label' => $sobject['label'],
                'value' => $sobject['name']
            ];
        }

        // Sort them by name
        ArrayHelper::multisort($describeOptions, 'label');

        return $describeOptions;
    }

    /**
     * @return array
     */
    private function getTabs(): array
    {
        return [
            'fields' => [
                'label' => Craft::t('force', 'Fields'),
                'url' => '#fields'
            ],
            'relations' => [
                'label' => Craft::t('force', 'Relationships'),
                'url' => '#relations'
            ]
        ];
    }

    /*******************************************
     * BASE PATHS
     *******************************************/

    /**
     * @return string
     */
    protected function getBaseCpPath(): string
    {
        return parent::getBaseCpPath() . '/sobjects';
    }

    /**
     * @return string
     */
    protected function getBaseActionPath(): string
    {
        return parent::getBaseActionPath() . '/sobjects';
    }
}
