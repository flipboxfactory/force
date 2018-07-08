<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers\collections;

use flipbox\force\transformers\DynamicModelError;
use flipbox\force\transformers\DynamicModelSuccess;
use yii\base\BaseObject;

class TransformerCollection extends BaseObject implements TransformerCollectionInterface
{
    use TransformerCollectionTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->transformers = [
            TransformerCollectionInterface::SUCCESS_KEY => [
                'class' => DynamicModelSuccess::class
            ],
            TransformerCollectionInterface::ERROR_KEY => [
                'class' => DynamicModelError::class
            ]
        ];
    }
}
