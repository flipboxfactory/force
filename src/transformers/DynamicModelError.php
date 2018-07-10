<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers;

use craft\base\ElementInterface;
use flipbox\force\Force;
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
     * @param ElementInterface|null $source
     * @param string|null $object
     * @return mixed
     */
    public function __invoke(
        $data,
        Scope $scope,
        string $identifier = null,
        ElementInterface $source = null,
        string $object = null
    ) {
        if (!is_array($data)) {
            $data = [$data];
        }

        $errors = $this->transformErrors($data);

        if (null !== $source) {
            $this->populateSource($source, $errors);
        }

        $model = new DynamicModel();
        $model->addErrors($errors);

        return $model;
    }

    /**
     * @param $object
     * @param array $errors
     */
    protected function populateSource($object, array $errors)
    {
        if (!is_object($object) || !method_exists($object, 'addErrors')) {
            Force::warning(
                "Unable to populate object errors.",
                __METHOD__
            );

            return;
        }

        $object->addErrors($errors);
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
