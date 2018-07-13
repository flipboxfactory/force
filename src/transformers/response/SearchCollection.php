<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/salesforce/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/salesforce
 */

namespace flipbox\force\transformers\response;

use flipbox\force\collections\Collection;
use flipbox\force\helpers\TransformerHelper;
use Flipbox\Skeleton\Helpers\ObjectHelper;
use Flipbox\Transform\Factory;
use Flipbox\Transform\Transformers\ArrayTransformer;
use Flipbox\Transform\Transformers\TransformerInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SearchCollection extends ArrayTransformer
{
    /**
     * @var callable|TransformerInterface|null
     */
    public $transformer;

    /**
     * @inheritdoc
     * @return Collection
     * @throws \Flipbox\Skeleton\Exceptions\InvalidConfigurationException
     */
    public function transform(array $data): Collection
    {
        $collection = new Collection();

        if (null === ($transformer = TransformerHelper::resolve($this->transformer))) {
            return $collection;
        }

        $data = Factory::item(
            $transformer,
            $data
        );

        ObjectHelper::configure(
            $collection,
            $data
        );

        return $collection;
    }
}
