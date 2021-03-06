<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\criteria\traits;

use flipbox\force\helpers\TransformerHelper;
use flipbox\force\transformers\collections\DynamicTransformerCollection;
use flipbox\force\transformers\collections\TransformerCollectionInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait TransformerCollectionTrait
{
    /**
     * @var TransformerCollectionInterface|array|null
     */
    protected $transformer = ['class' => DynamicTransformerCollection::class];

    /**
     * @param $value
     * @return $this
     */
    public function transformer($value)
    {
        return $this->setTransformer($value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setTransformer($value)
    {
        if (empty($value)) {
            $this->transformer = null;
            return $this;
        }

        if (is_string($value)) {
            if (TransformerHelper::isTransformerCollectionClass($value)) {
                $value = ['class' => $value];
            } else {
                $value = ['handle' => [$value]];
            }
        }

        if (array_key_exists('class', $value)) {
            $this->transformer = $value;
            return $this;
        }

        TransformerHelper::populateTransformerCollection(
            $this->getTransformer(),
            $value
        );

        return $this;
    }

    /**
     * @return TransformerCollectionInterface|null
     */
    public function getTransformer()
    {
        // Prevent subsequent resolves (since it already didn't)
        if (null === ($this->transformer = TransformerHelper::resolveCollection($this->transformer))) {
            $this->transformer = false;
            return null;
        }

        return $this->transformer;
    }
}
