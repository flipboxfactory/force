<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers;

use flipbox\force\transformers\error\Interpret;
use Flipbox\Transform\Factory;
use yii\base\DynamicModel;

/**
 * This transformer will take an API response and create/populate a User element.
 */
class DynamicModelError
{
    /**
     * @param array $data
     * @return mixed
     */
    public function __invoke(array $data)
    {
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
