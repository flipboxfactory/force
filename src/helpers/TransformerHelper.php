<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\helpers;

use flipbox\force\Force;
use flipbox\force\transformers\collections\TransformerCollection;
use flipbox\force\transformers\collections\TransformerCollectionInterface;
use Flipbox\Skeleton\Helpers\ObjectHelper;
use Flipbox\Transform\Helpers\TransformerHelper as BaseTransformerHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class TransformerHelper extends BaseTransformerHelper
{
    /**
     * @inheritdoc
     */
    public static function resolve($transformer = null)
    {
        if (empty($transformer)) {
            return null;
        }

        return parent::resolve($transformer);
    }

    /**
     * @param $transformer
     * @return bool
     */
    public static function isTransformerCollection($transformer): bool
    {
        return $transformer instanceof TransformerCollectionInterface;
    }

    /**
     * @param $transformer
     * @return bool
     */
    public static function isTransformerCollectionClass($transformer): bool
    {
        return is_string($transformer) && is_subclass_of($transformer, TransformerCollectionInterface::class);
    }

    /**
     * @param TransformerCollectionInterface|array|string|null $transformer
     * @param TransformerCollectionInterface|array|null $default
     * @return TransformerCollectionInterface|null
     */
    public static function resolveCollection($transformer = null, $default = ['class' => TransformerCollection::class])
    {
        if ($transformer === false) {
            return null;
        }

        if ($transformer === null && $default !== null) {
            $transformer = $default;
        }

        if (null !== ($collection = static::returnCollectionFromTransformer($transformer))) {
            return $collection;
        }

        if (is_array($transformer)) {
            try {
                $class = ObjectHelper::checkConfig($transformer, TransformerCollectionInterface::class);

                /** @var TransformerCollectionInterface $collection */
                $collection = new $class();

                static::populateTransformerCollection(
                    $collection,
                    $transformer
                );

                return $collection;
            } catch (\Throwable $e) {
                Force::warning(sprintf(
                    "An exception was thrown while trying to resolve transformer collection: '%s'",
                    (string)$e->getMessage()
                ));
            }
        }

        return null;
    }

    /**
     * @param TransformerCollectionInterface|string $transformer
     * @return null|TransformerCollectionInterface
     */
    protected static function returnCollectionFromTransformer($transformer)
    {
        if (static::isTransformerCollection($transformer)) {
            return $transformer;
        }

        if (static::isTransformerCollectionClass($transformer)) {
            return new $transformer();
        }

        return null;
    }


    /**
     * @param TransformerCollectionInterface|null $collection
     * @param array $config
     * @return TransformerCollectionInterface|null
     */
    public static function populateTransformerCollection(
        TransformerCollectionInterface $collection = null,
        array $config = []
    ) {
        if ($collection === null) {
            return $collection;
        }

        foreach ($config as $name => $value) {
            $setter = 'set' . $name;
            if (method_exists($collection, $setter)) {
                $collection->$setter($value);
                continue;
            }

            if (property_exists($collection, $name)) {
                $collection->{$name} = $value;
                continue;
            }
        }

        return $collection;
    }
}
