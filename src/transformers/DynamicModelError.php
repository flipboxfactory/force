<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers;

use flipbox\force\transformers\error\Interpret;
use Flipbox\Transform\Factory;
use Flipbox\Transform\Scope;
use Flipbox\Transform\Transformers\AbstractTransformer;
use yii\base\DynamicModel;

/**
 * This transformer will take an API response and create/populate a User element.
 */
class DynamicModelError extends AbstractTransformer
{
    /**
     * @param $data
     * @param Scope $scope
     * @param string|null $identifier
     * @return mixed
     */
    public function __invoke(
        $data,
        Scope $scope,
        string $identifier = null
    ) {
        if (!is_array($data)) {
            $data = [$data];
        }

        $errors = $this->transformErrors($data);

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
