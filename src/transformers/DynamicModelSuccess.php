<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers;

use yii\base\DynamicModel;

class DynamicModelSuccess
{
    /**
     * @param array $data
     * @return mixed
     */
    public function __invoke(array $data)
    {
        return new DynamicModel(array_keys($data), $data);
    }
}
