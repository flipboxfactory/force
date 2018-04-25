<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\controllers\view;

use Craft;
use craft\helpers\UrlHelper;
use flipbox\force\records\Query;
use flipbox\force\web\assets\soql\SOQL;
use yii\web\Response;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class QueriesController extends AbstractController
{
    /**
     * The template base path
     */
    const TEMPLATE_BASE = parent::TEMPLATE_BASE . DIRECTORY_SEPARATOR . 'queries';

    /**
     * The index view template path
     */
    const TEMPLATE_INDEX = self::TEMPLATE_BASE . DIRECTORY_SEPARATOR . 'index';

    /**
     * The index view template path
     */
    const TEMPLATE_VIEW = self::TEMPLATE_BASE . DIRECTORY_SEPARATOR . 'view';

    /**
     * @return \flipbox\force\cp\services\QueryManager
     */
    protected function queryService()
    {
        return $this->module->getQueryManager();
    }

    /**
     * Index/List
     *
     * @return Response
     */
    public function actionIndex(): Response
    {
        $variables = [];
        $this->baseVariables($variables);

        $variables['queries'] = $this->queryService()->findAll();

        return $this->renderTemplate(
            static::TEMPLATE_INDEX,
            $variables
        );
    }

    /**
     * View
     *
     * @param null $identifier
     * @return Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionView($identifier = null): Response
    {
        Craft::$app->getView()->registerAssetBundle(SOQL::class);
        $query = $this->queryService()->get($identifier);

        $variables = [];
        $this->viewVariables($variables, $query);

        $variables['query'] = $query;
        $variables['fullPageForm'] = true;

        return $this->renderTemplate(static::TEMPLATE_VIEW, $variables);
    }


    /*******************************************
     * BASE PATHS
     *******************************************/

    /**
     * @return string
     */
    protected function getBaseCpPath(): string
    {
        return parent::getBaseCpPath() . '/queries';
    }

    /**
     * @return string
     */
    protected function getBaseActionPath(): string
    {
        return parent::getBaseActionPath() . '/queries';
    }


    /*******************************************
     * UPDATE VARIABLES
     *******************************************/

    /**
     * @param array $variables
     * @param Query $query
     */
    protected function viewVariables(array &$variables, Query $query)
    {
        $this->baseVariables($variables);
        $variables['title'] .= ' - ' . $query->name;
        $variables['continueEditingUrl'] = $this->getBaseContinueEditingUrl('/' . $query->getId());
        $variables['crumbs'][] = [
            'label' => $query->name,
            'url' => UrlHelper::url($variables['continueEditingUrl'])
        ];
    }

    /**
     * @inheritdoc
     */
    protected function baseVariables(array &$variables = [])
    {
        parent::baseVariables($variables);

        $title = Craft::t('force', "Queries");
        $variables['title'] .= ' ' . $title;

        // Breadcrumbs
        $variables['crumbs'][] = [
            'label' => $title,
            'url' => UrlHelper::url($this->getBaseCpPath())
        ];
    }
}
