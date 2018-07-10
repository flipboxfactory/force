<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\actions\fields;

use Craft;
use craft\base\ElementInterface;
use craft\helpers\StringHelper;
use flipbox\ember\actions\traits\Manage;
use flipbox\ember\helpers\SiteHelper;
use flipbox\force\actions\traits\ElementResolverTrait;
use flipbox\force\actions\traits\FieldResolverTrait;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use flipbox\force\records\ObjectAssociation;
use yii\base\Action;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class CreateItem extends Action
{
    use ElementResolverTrait,
        FieldResolverTrait,
        Manage;

    /**
     * @param string $field
     * @param string $element
     * @param string|null $id
     * @return mixed
     * @throws HttpException
     * @throws \yii\base\Exception
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function run(string $field, string $element, string $id = null)
    {
        $field = $this->resolveField($field);
        $element = $this->resolveElement($element);

        $record = Force::getInstance()->getObjectAssociations()->create([
            'fieldId' => $field->id,
            'objectId' => $id,
            'elementId' => $element->getId(),
            'siteId' => SiteHelper::ensureSiteId($element->siteId),
        ]);

        return $this->runInternal($field, $element, $record);
    }

    /**
     * @param Objects $field
     * @param ElementInterface $element
     * @param ObjectAssociation $record
     * @return mixed
     * @throws \yii\base\Exception
     * @throws \yii\web\UnauthorizedHttpException
     */
    protected function runInternal(
        Objects $field,
        ElementInterface $element,
        ObjectAssociation $record
    ) {
        // Check access
        if (($access = $this->checkAccess($field, $element, $record)) !== true) {
            return $access;
        }

        if (null === ($html = $this->performAction($field, $record))) {
            return $this->handleFailResponse($html);
        }

        return $this->handleSuccessResponse($html);
    }

    /**
     * @param Objects $field
     * @param ObjectAssociation $record
     * @return array
     * @throws \yii\base\Exception
     */
    public function performAction(
        Objects $field,
        ObjectAssociation $record
    ): array {

        $view = Craft::$app->getView();

        return [
            'html' => $view->renderTemplateMacro(
                Objects::INPUT_TEMPLATE_PATH,
                'createItem',
                [
                    'field' => $field,
                    'record' => $record,
                    'objectLabel' => Force::getInstance()->getObjectsField()->getObjectLabel($field)
                ]
            ),
            'headHtml' => $view->getHeadHtml(),
            'footHtml' => $view->getBodyHtml()
        ];
    }
}
