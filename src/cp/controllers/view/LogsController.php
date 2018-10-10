<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\controllers\view;

use Craft;
use craft\helpers\UrlHelper;
use flipbox\ember\controllers\LogViewerTrait;
use flipbox\force\Force;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;
use yii\data\Pagination;
use yii\web\HttpException;
use yii\web\Response;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class LogsController extends AbstractController
{
    use LogViewerTrait;

    /**
     * The template base path
     */
    const TEMPLATE_BASE = parent::TEMPLATE_BASE . DIRECTORY_SEPARATOR . 'logs';

    /**
     * The index view template path
     */
    const TEMPLATE_INDEX = self::TEMPLATE_BASE . DIRECTORY_SEPARATOR . 'index';

    /**
     * @inheritdoc
     */
    protected function logFile(): string
    {
        return Force::getLogFile();
    }

    /**
     * @return Response
     * @throws HttpException
     */
    public function actionIndex(): Response
    {
        $variables = [];
        $this->baseVariables($variables);

        $variables['data'] = $this->getLogItems();

        return $this->renderTemplate(
            static::TEMPLATE_INDEX,
            $variables
        );
    }

    /**
     * @return DataProviderInterface
     * @throws HttpException
     */
    protected function getLogItems(): DataProviderInterface
    {
        $file = Craft::getAlias($this->logFile());

        if (!is_file($file)) {
            throw new HttpException("Unable to find log file.");
        }

        return new ArrayDataProvider([
            'allModels' => $this->parseFile($file),
            'sort' => [
                'attributes' => [
                    'time' => ['default' => SORT_DESC],
                    'level' => ['default' => SORT_DESC]
                ],
            ],
            'pagination' => [
                'class' => Pagination::class,
                'pageSizeParam' => 'size',
                'pageParam' => 'page',
                'pageSizeLimit' => 'limit',
                'defaultPageSize' => 200,
            ]
        ]);
    }

    /*******************************************
     * BASE PATHS
     *******************************************/

    /**
     * @return string
     */
    protected function getBaseCpPath(): string
    {
        return parent::getBaseCpPath() . '/logs';
    }

    /**
     * @return string
     */
    protected function getBaseActionPath(): string
    {
        return parent::getBaseActionPath() . '/logs';
    }

    /*******************************************
     * VARIABLES
     *******************************************/

    /**
     * @inheritdoc
     */
    protected function baseVariables(array &$variables = [])
    {
        parent::baseVariables($variables);

        $title = Craft::t('force', "Data");
        $variables['title'] .= ' ' . $title;

        // Breadcrumbs
        $variables['crumbs'][] = [
            'label' => $title,
            'url' => UrlHelper::url($this->getBaseCpPath())
        ];
    }
}
