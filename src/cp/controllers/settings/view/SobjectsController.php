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
use flipbox\force\transformers\collections\TransformerCollection;
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
    const TEMPLATE_BASE = parent::TEMPLATE_BASE . DIRECTORY_SEPARATOR . 'objects';

    /**
     * The index view template path
     */
    const TEMPLATE_INDEX = self::TEMPLATE_BASE . DIRECTORY_SEPARATOR . 'index';

    /**
     * @return Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex(): Response
    {
        $variables = [];
        $this->baseVariables($variables);

        $object = Craft::$app->getRequest()->getParam('object');
        $describedSobject = null;

        if ($object !== null) {
            $describedSobject = Force::getInstance()->getResources()->getObject()->getCriteria([
                'object' => $object,
                'transformer' => TransformerCollection::class
            ])->describe();
        }

        $variables['describedSobject'] = $describedSobject;
        $variables['objectOptions'] = $this->getObjectOptions();
        $variables['tabs'] = $this->getTabs();

        return $this->renderTemplate(
            static::TEMPLATE_INDEX,
            $variables
        );
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    private function getObjectOptions()
    {
        $describe = Force::getInstance()->getResources()->getGeneral()->getCriteria([
            'transformer' => TransformerCollection::class
        ])->describe();
        $describeOptions = [];

        foreach (ArrayHelper::getValue($describe, 'objects', []) as $object) {
            $describeOptions[] = [
                'label' => $object['label'],
                'value' => $object['name']
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
        return parent::getBaseCpPath() . '/objects';
    }

    /**
     * @return string
     */
    protected function getBaseActionPath(): string
    {
        return parent::getBaseActionPath() . '/objects';
    }
}
