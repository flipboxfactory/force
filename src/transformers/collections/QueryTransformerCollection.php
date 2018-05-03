<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers\collections;

use flipbox\force\transformers\ResponseToDynamicModel;
use Flipbox\Salesforce\Transformers\Collections\TransformerCollectionInterface;
use Flipbox\Salesforce\Transformers\Error\Interpret;
use Flipbox\Salesforce\Transformers\Response\QueryCollection;
use Flipbox\Transform\Factory;

class QueryTransformerCollection extends DynamicTransformerCollection
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Wrap the error interpreter with an 'error' key
        $this->defaultTransformers[TransformerCollectionInterface::ERROR_KEY] = function ($data) {
            return ['errors' => Factory::item(
                new Interpret(),
                $data
            )];
        };

        // Wrap the data transformer with an 'items' key and select the 'records'
        $this->defaultTransformers[TransformerCollectionInterface::SUCCESS_KEY] = function ($data) {
            return ['items' => Factory::Collection(
                new ResponseToDynamicModel(),
                $data['records'] ?? []
            )];
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
