<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/skeleton/blob/master/LICENSE
 * @link       https://github.com/flipbox/skeleton
 */

namespace flipbox\craft\salesforce\criteria;

use Craft;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class AbstractCriteria extends BaseObject
{
    /**
     * @param array $properties
     * @return static
     */
    public function populate(array $properties = [])
    {
        Craft::configure(
            $this,
            $properties
        );

        return $this;
    }
}
