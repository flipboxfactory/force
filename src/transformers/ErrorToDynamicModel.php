<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers;

use Flipbox\Salesforce\Transformers\Error\Interpret;
use Flipbox\Transform\Factory;
use Flipbox\Transform\Scope;
use Flipbox\Transform\Transformers\AbstractTransformer;
use Flipbox\Transform\Transformers\Traits\ArrayToObject;
use yii\base\DynamicModel;
use yii\base\Model;

/**
 * This transformer will take an API response and create/populate a User element.
 */
class ErrorToDynamicModel extends AbstractTransformer
{
    use ArrayToObject;

    /**
     * @inheritdoc
     * @param null $source
     * @return mixed|DynamicModel
     */
    public function __invoke($data, Scope $scope, string $identifier = null, $source = null)
    {
        return $this->transform(
            $this->normalizeData($data, $scope),
            $source
        );
    }

    /**
     * @inheritdoc
     * @return DynamicModel
     */
    public function transform(array $data, $source = null): DynamicModel
    {
        $errors = $this->transformErrors($data);

        if ($source instanceof Model) {
            $source->addErrors($errors);
        }

        $model = new DynamicModel();
        $model->addErrors($errors);

        return $model;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function transformErrors(array $data): array
    {
        $errors = Factory::item(
            new Interpret,
            $data
        );

        if (!$errors) {
            $errors = [$errors];
        }

        return array_filter($errors);
    }
}
