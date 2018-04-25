<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers;

use Flipbox\Transform\Transformers\AbstractTransformer;
use Flipbox\Transform\Transformers\Traits\ArrayToObject;
use yii\base\DynamicModel;

/**
 * This transformer will take an API response and create/populate a User element.
 */
class ResponseToDynamicModel extends AbstractTransformer
{
    use ArrayToObject;

    /**
     * @param array $data
     * @return DynamicModel
     */
    public function transform(array $data): DynamicModel
    {
        return new DynamicModel(array_keys($data), $data);
    }
}
