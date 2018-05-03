<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\controllers\view;

use Craft;
use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;
use flipbox\flux\helpers\TransformerHelper;
use flipbox\force\Force;
use Flipbox\Salesforce\Resources\Query as QueryResource;
use Flipbox\Salesforce\Resources\SObject\Row\Delete as DeleteSObjectResource;
use Flipbox\Salesforce\Resources\SObject\Row\Get as GetSObjectResource;
use Flipbox\Salesforce\Resources\SObject\Row\Upsert as UpsertSObjectResource;
use Flipbox\Salesforce\Transformers\Collections\TransformerCollectionInterface;
use yii\web\Response;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class DataController extends AbstractController
{
    /**
     * The template base path
     */
    const TEMPLATE_BASE = parent::TEMPLATE_BASE . DIRECTORY_SEPARATOR . 'data';

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

        $variables['resourceOptions'] = $this->getResourceOptions();
        $variables['contextOptions'] = $this->getContextTypes();

        $variables['resource'] = $this->getResourceFromRequest();
        $variables['handle'] = $this->getHandleFromRequest();
        $variables['context'] = $this->getContextFromRequest();

        $variables['transformer'] = $this->getTransformer();

        return $this->renderTemplate(
            static::TEMPLATE_INDEX,
            $variables
        );
    }

    /**
     * @return callable|\Flipbox\Transform\Transformers\TransformerInterface|null
     */
    private function getTransformer()
    {
        if (null === ($resource = $this->getResourceFromRequest())) {
            return null;
        }

        $handle = $this->getHandleFromRequest();
        $context = $this->getContextFromRequest();

        return Force::getInstance()->getTransformers()->find(
            TransformerHelper::eventName(array_merge($handle, [$context])),
            $resource
        );
    }

    /**
     * @return mixed
     */
    private function getResourceFromRequest()
    {
        return Craft::$app->getRequest()->getParam('resource');
    }

    /**
     * @return array
     */
    private function getHandleFromRequest(): array
    {
        $handle = Craft::$app->getRequest()->getParam('handle', []);

        if (is_string($handle)) {
            $handle = StringHelper::split($handle, ':');
        }

        return array_map('strtolower', $handle);
    }

    /**
     * @return array
     */
    private function getContextFromRequest()
    {
        return Craft::$app->getRequest()->getParam('context');
    }


    /**
     * @return array
     */
    private function getResourceOptions(): array
    {
        return array_merge(
            $this->getSobjectResourceOptions(),
            $this->getQueryResourceOptions()
        );
    }

    /**
     * @return array
     */
    private function getSobjectResourceOptions(): array
    {
        return [
            GetSObjectResource::class => 'SObject: Get Row',
            UpsertSObjectResource::class => 'SObject: Upsert Row',
            DeleteSObjectResource::class => 'SObject: Delete Row'
        ];
    }

    /**
     * @return array
     */
    private function getQueryResourceOptions(): array
    {
        return [
            QueryResource::class => 'Query',
        ];
    }

    /**
     * @return array
     */
    private function getContextTypes(): array
    {
        return [
            TransformerCollectionInterface::SUCCESS_KEY => 'Success',
            TransformerCollectionInterface::ERROR_KEY => 'Error',
            'payload' => 'SObject Payload',
            'id' => 'SObject Id'
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
        return parent::getBaseCpPath() . '/data';
    }

    /**
     * @return string
     */
    protected function getBaseActionPath(): string
    {
        return parent::getBaseActionPath() . '/data';
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
