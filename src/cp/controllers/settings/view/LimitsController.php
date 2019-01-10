<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\cp\controllers\settings\view;

use flipbox\craft\salesforce\transformers\DynamicModelResponse;
use flipbox\craft\salesforce\criteria\InstanceCriteria;
use yii\web\Response;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class LimitsController extends AbstractController
{
    /**
     * The template base path
     */
    const TEMPLATE_BASE = parent::TEMPLATE_BASE . DIRECTORY_SEPARATOR . 'limits';

    /**
     * The index view template path
     */
    const TEMPLATE_INDEX = self::TEMPLATE_BASE . DIRECTORY_SEPARATOR . 'index';

    /**
     * @return Response
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex(): Response
    {
        $variables = [];
        $this->baseVariables($variables);

        $criteria = new InstanceCriteria();
        $variables['limits'] = call_user_func_array(
            new DynamicModelResponse(),
            [
                $criteria->limits()
            ]
        );

        return $this->renderTemplate(
            static::TEMPLATE_INDEX,
            $variables
        );
    }

    /*******************************************
     * BASE PATHS
     *******************************************/

    /**
     * @return string
     */
    protected function getBaseCpPath(): string
    {
        return parent::getBaseCpPath() . '/limits';
    }

    /**
     * @return string
     */
    protected function getBaseActionPath(): string
    {
        return parent::getBaseActionPath() . '/limits';
    }
}
