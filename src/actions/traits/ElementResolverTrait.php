<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\traits;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait ElementResolverTrait
{
    /**
     * @param string $element
     * @return ElementInterface|Element
     * @throws HttpException
     */
    protected function resolveElement(string $element): ElementInterface
    {
        if (null === ($element = Craft::$app->getElements()->getElementById($element))) {
            throw new HttpException(400, 'Invalid element');
        };

        return $element;
    }
}