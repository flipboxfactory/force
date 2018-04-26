<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers;

use flipbox\force\Force;
use flipbox\flux\helpers\TransformerHelper;
use Flipbox\Transform\Factory;
use Flipbox\Transform\Scope;
use Flipbox\Transform\Transformers\AbstractTransformer;
use yii\base\DynamicModel;

/**
 * This transformer will take an API response and create/populate a User element.
 */
class ResponseToDynamicModel extends AbstractTransformer
{
    /**
     * @inheritdoc
     */
    public function __invoke($data, Scope $scope, string $identifier = null)
    {
        if (!is_array($data)) {
            $data = [$data];
        }

        return $this->transform($data);
    }

    /**
     * @param array $data
     * @return DynamicModel
     */
    protected function transform(array $data): DynamicModel
    {
        return new DynamicModel(array_keys($data), $data);
    }
}
