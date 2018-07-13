<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers;

use craft\base\ElementInterface;
use Flipbox\Transform\Transformers\AbstractTransformer;
use yii\base\DynamicModel;

class DynamicModelSuccess extends AbstractTransformer
{
    /**
     * @param array $data
     * @param ElementInterface|null $source
     * @param string|null $object
     * @return mixed
     */
    public function __invoke(
        array $data,
        ElementInterface $source = null,
        string $object = null
    ) {
        return new DynamicModel(array_keys($data), $data);
    }
}
