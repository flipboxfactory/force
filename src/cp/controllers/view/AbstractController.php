<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\cp\controllers\view;

use Craft;
use craft\web\Controller;
use flipbox\craft\ember\helpers\UrlHelper;
use flipbox\craft\salesforce\cp\Cp as CpModule;
use flipbox\craft\salesforce\Force;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property CpModule $module
 */
abstract class AbstractController extends Controller
{
    /**
     * The index view template path
     */
    const TEMPLATE_BASE = 'salesforce' . DIRECTORY_SEPARATOR . '_cp';

    /*******************************************
     * BASE PATHS
     *******************************************/

    /**
     * @return string
     */
    protected function getBaseActionPath(): string
    {
        return Force::getInstance()->getUniqueId() . '/cp';
    }

    /**
     * @return string
     */
    protected function getBaseCpPath(): string
    {
        return Force::getInstance()->getUniqueId();
    }

    /**
     * @param string $endpoint
     * @return string
     */
    protected function getBaseContinueEditingUrl(string $endpoint = ''): string
    {
        return $this->getBaseCpPath() . $endpoint;
    }

    /*******************************************
     * VARIABLES
     *******************************************/

    /**
     * @inheritdoc
     */
    protected function baseVariables(array &$variables = [])
    {
        $module = Force::getInstance();

        $title = Craft::t('salesforce', "Salesforce");

        // Settings
        $variables['settings'] = $module->getSettings();
        $variables['title'] = $title;

        // Path to controller actions
        $variables['baseActionPath'] = $this->getBaseActionPath();

        // Path to CP
        $variables['baseCpPath'] = $this->getBaseCpPath();

        // Set the "Continue Editing" URL
        $variables['continueEditingUrl'] = $this->getBaseCpPath();

        // Select our sub-nav
        if (!$activeSubNav = Craft::$app->getRequest()->getSegment(2)) {
            $activeSubNav = 'queries';
        }
        $variables['selectedSubnavItem'] = 'salesforce.' . $activeSubNav;

        // Breadcrumbs
        $variables['crumbs'][] = [
            'label' => $title,
            'url' => UrlHelper::url(Force::getInstance()->getUniqueId())
        ];
    }

    /*******************************************
     * UPSERT VARIABLES
     *******************************************/

    /**
     * @param array $variables
     */
    protected function insertVariables(array &$variables)
    {
        // apply base view variables
        $this->baseVariables($variables);

        // Set the "Continue Editing" URL
        $variables['continueEditingUrl'] = $this->getBaseContinueEditingUrl('/{id}');

        // Append title
        $variables['title'] .= ' - ' . Craft::t('salesforce', 'New');
    }
}
