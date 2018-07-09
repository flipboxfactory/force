<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\cp\actions\fields;

use Craft;
use craft\base\ElementInterface;
use flipbox\ember\actions\traits\Manage;
use flipbox\force\criteria\ObjectAccessorCriteria;
use flipbox\force\criteria\ObjectAccessorCriteriaInterface;
use flipbox\force\fields\Objects;
use yii\base\Action;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class CreateRow extends Action
{
    use traits\ElementResolverTrait,
        traits\FieldResolverTrait,
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

        $criteria = new ObjectAccessorCriteria([
            'object' => $field->object,
            'id' => $id
        ]);

        return $this->runInternal($field, $element, $criteria);
    }

    /**
     * @param Objects $field
     * @param ElementInterface $element
     * @param ObjectAccessorCriteriaInterface $criteria
     * @return mixed
     * @throws \yii\base\Exception
     * @throws \yii\web\UnauthorizedHttpException
     */
    protected function runInternal(
        Objects $field,
        ElementInterface $element,
        ObjectAccessorCriteriaInterface $criteria
    ) {
        // Check access
        if (($access = $this->checkAccess($field, $element, $criteria)) !== true) {
            return $access;
        }

        if (null === ($html = $this->performAction($field, $criteria))) {
            return $this->handleFailResponse($html);
        }

        return $this->handleSuccessResponse($html);
    }

    /**
     * @param Objects $field
     * @param ObjectAccessorCriteriaInterface $criteria
     * @return array
     * @throws \yii\base\Exception
     */
    public function performAction(
        Objects $field,
        ObjectAccessorCriteriaInterface $criteria
    ): array {

        $view = Craft::$app->getView();

        return [
            'html' => $view->renderTemplateMacro(
                'force/_components/fieldtypes/SObjects/input',
                'createRow',
                [
                    'field' => $field,
                    'value' => $criteria
                ]
            ),
            'headHtml' => $view->getHeadHtml(),
            'footHtml' => $view->getBodyHtml()
        ];
    }
}
