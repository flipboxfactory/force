<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\query;

use flipbox\force\records\SOQL;
use flipbox\force\transformers\DynamicModelResponse;
use flipbox\force\criteria\QueryCriteria;
use yii\base\DynamicModel;
use yii\db\ActiveRecord;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class PreviewQuery extends CreateQuery
{
    public $statusCodeFail = 200;

    /**
     * @inheritdoc
     * @param SOQL $record
     */
    public function runInternal(ActiveRecord $record)
    {
        // PopulateOrganizationTypeTrait
        $this->populate($record);

        // Check access
        if (($access = $this->checkAccess($record)) !== true) {
            return $access;
        }

        $model = $this->preview($record);

        if ($model->hasErrors()) {
            $this->handleFailResponse($model);

            return [
                'query' => $record->build(),
                'errors' => $model->getErrors()
            ];
        }

        $this->handleSuccessResponse($model);

        return [
            'query' => $record->build(),
            'result' => $model
        ];
    }

    /**
     * @param SOQL $record
     * @return DynamicModel
     */
    protected function preview(SOQL $record): DynamicModel
    {
        $criteria = new QueryCriteria([
            'query' => $record
        ]);

        $response = $criteria->fetch();

        /** @var DynamicModel $model */
        $model = call_user_func_array(
            new DynamicModelResponse(),
            [
                $response
            ]
        );

        return $model;
    }

    /**
     * We're not actually saving a query ... so prevent it from happening
     *
     * @inheritdoc
     * @throws HttpException
     */
    protected function performAction(ActiveRecord $record): bool
    {
        throw new HttpException(400, 'Unable to perform action');
    }
}
