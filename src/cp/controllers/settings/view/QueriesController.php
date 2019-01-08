<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\controllers\settings\view;

use Craft;
use craft\helpers\UrlHelper;
use flipbox\force\query\settings\DynamicQuerySettings;
use flipbox\force\records\SOQL;
use Flipbox\Salesforce\Query\DynamicQueryBuilder;
use yii\web\HttpException;
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
    const TEMPLATE_UPSERT = self::TEMPLATE_BASE . DIRECTORY_SEPARATOR . 'upsert';

    /**
     * @return Response
     */
    public function actionIndex(): Response
    {
        $variables = [];
        $this->baseVariables($variables);

        $variables['queries'] = SOQL::findAll([]);

        return $this->renderTemplate(
            static::TEMPLATE_INDEX,
            $variables
        );
    }

    /**
     * @param null $identifier
     * @param SOQL|null $query
     * @return Response
     * @throws HttpException
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     */
    public function actionUpsert($identifier = null, SOQL $query = null): Response
    {
        if (null === $query) {
            if (null === $identifier) {
                $query = new SOQL();
            } else {
                $query = SOQL::getOne($identifier);
            }
        }

        $variables = [];
        if ($query->getIsNewRecord()) {
            $this->insertVariables($variables);
        } else {
            $this->updateVariables($variables, $query);
        }

        $queryBuilder = $query->getCriteria()->getQuery();
        if (!$queryBuilder instanceof DynamicQueryBuilder) {
            throw new HttpException("Invalid Query Type");
        }

        $settings = new DynamicQuerySettings($queryBuilder);

        if ($query->hasErrors('settings')) {
            $settings->addError('query', $query->getFirstError('settings'));
        }

        $variables['querySettings'] = $settings;
        $variables['query'] = $query;
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
     * @param SOQL $query
     */
    protected function updateVariables(array &$variables, SOQL $query)
    {
        $this->baseVariables($variables);
        $variables['title'] .= ' - ' . Craft::t('force', 'Edit') . ' ' . $query->name;
        $variables['continueEditingUrl'] = $this->getBaseContinueEditingUrl('/' . $query->getId());
        $variables['crumbs'][] = [
            'label' => $query->name,
            'url' => UrlHelper::url($variables['continueEditingUrl'])
        ];
    }
}
