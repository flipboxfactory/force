<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\controllers\settings\view;

use Craft;
use craft\helpers\UrlHelper;
use flipbox\force\query\DynamicQueryBuilder;
use flipbox\force\query\settings\DynamicQuerySettings;
use flipbox\force\records\Connection;
use yii\web\HttpException;
use yii\web\Response;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ConnectionsController extends AbstractController
{
    /**
     * The template base path
     */
    const TEMPLATE_BASE = parent::TEMPLATE_BASE . DIRECTORY_SEPARATOR . 'connections';

    /**
     * The index view template path
     */
    const TEMPLATE_INDEX = self::TEMPLATE_BASE . DIRECTORY_SEPARATOR . 'index';

    /**
     * The index view template path
     */
    const TEMPLATE_UPSERT = self::TEMPLATE_BASE . DIRECTORY_SEPARATOR . 'upsert';

    /**
     * @return \flipbox\force\cp\services\ConnectionManager
     */
    protected function connectionService()
    {
        return $this->module->getConnectionManager();
    }

    /**
     * @return Response
     */
    public function actionIndex(): Response
    {
        $variables = [];
        $this->baseVariables($variables);

        $variables['connections'] = $this->connectionService()->findAll();
        $variables['types'] = $this->connectionService()->getTypes();

        return $this->renderTemplate(
            static::TEMPLATE_INDEX,
            $variables
        );
    }

    /**
     * @param null $identifier
     * @param Connection|null $connection
     * @return Response
     * @throws HttpException
     * @throws \flipbox\ember\exceptions\NotFoundException
     */
    public function actionUpsert($identifier = null, Connection $connection = null): Response
    {

        if (null === $connection) {
            if (null === $identifier) {
                $connection = $this->connectionService()->create();
            } else {
                $connection = $this->connectionService()->get($identifier);
            }
        }


        $variables = [];
        if ($connection->getIsNewRecord()) {
            $this->insertVariables($variables);
        } else {
            $this->updateVariables($variables, $connection);
        }

        $variables['types'] = $this->connectionService()->getTypes();
        $variables['connection'] = $connection;
        $variables['fullPageForm'] = true;

        return $this->renderTemplate(static::TEMPLATE_UPSERT, $variables);
    }


    /*******************************************
     * BASE PATHS
     *******************************************/

    /**
     * @return string
     */
    protected function getBaseCpPath(): string
    {
        return parent::getBaseCpPath() . '/connections';
    }

    /**
     * @return string
     */
    protected function getBaseActionPath(): string
    {
        return parent::getBaseActionPath() . '/connections';
    }

    /*******************************************
     * UPDATE VARIABLES
     *******************************************/

    /**
     * @param array $variables
     * @param Connection $connection
     */
    protected function updateVariables(array &$variables, Connection $connection)
    {
        $this->baseVariables($variables);
        $variables['title'] .= ' - ' . Craft::t('force', 'Edit') . ' ' . $connection->handle;
        $variables['continueEditingUrl'] = $this->getBaseContinueEditingUrl('/' . $connection->getId());
        $variables['crumbs'][] = [
            'label' => $connection->handle,
            'url' => UrlHelper::url($variables['continueEditingUrl'])
        ];
    }
}
