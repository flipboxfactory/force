<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers\collections;

use flipbox\force\transformers\DynamicModelSuccess;
use flipbox\force\transformers\error\Interpret;
use flipbox\force\transformers\response\QueryCollection;
use Flipbox\Transform\Factory;

class SearchTransformerCollection extends DynamicTransformerCollection
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Wrap the error interpreter with an 'error' key
        $this->transformers[TransformerCollectionInterface::ERROR_KEY] = function ($data) {
            return [
                'errors' => Factory::item(
                    new Interpret(),
                    $data
                )
            ];
        };

        // Wrap the data transformer with an 'items' key and select the 'records'
        $this->transformers[TransformerCollectionInterface::SUCCESS_KEY] = function ($data) {
            return [
                'items' => Factory::Collection(
                    new DynamicModelSuccess(),
                    $data['records'] ?? []
                )
            ];
        };
    }

    /**
     * @inheritdoc
     */
    public function getTransformer(string $key): QueryCollection
    {
        return new QueryCollection([
            'transformer' => parent::getTransformer($key)
        ]);
    }
}
