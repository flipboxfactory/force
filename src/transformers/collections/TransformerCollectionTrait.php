<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/salesforce/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/salesforce
 */

namespace flipbox\force\transformers\collections;

use flipbox\force\helpers\TransformerHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait TransformerCollectionTrait
{
    /**
     * @var array
     */
    protected $transformers = [];

    /*******************************************
     * TRANSFORMER
     *******************************************/

    /**
     * @param $transformers
     * @return $this
     */
    public function setTransformers($transformers)
    {
        if (!is_array($transformers)) {
            $transformers = empty($transformers) ? [] : ['default' => $transformers];
        }

        foreach ($transformers as $key => $value) {
            $this->addTransformer($key, $value);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param $transformer
     * @return $this
     */
    public function addTransformer(string $key, $transformer)
    {
        $this->transformers[$key] = $transformer;
        return $this;
    }

    /**
     * @param string $key
     * @return callable|null
     * @throws \Flipbox\Skeleton\Exceptions\InvalidConfigurationException
     */
    public function getTransformer(string $key)
    {
        if (!array_key_exists($key, $this->transformers)) {
            return null;
        }

        return TransformerHelper::resolve($this->transformers[$key]);
    }
}
