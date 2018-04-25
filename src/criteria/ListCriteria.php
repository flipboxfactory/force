<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\criteria;

use flipbox\force\Force;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ListCriteria extends ResourceCriteria implements CriteriaInterface
{
    /**
     * @param array $criteria
     * @param null $source
     * @return mixed
     */
    public function getResources(array $criteria = [], $source = null)
    {
        $this->prepare($criteria);
        return Force::getInstance()->getResources()->getGeneral()->getResourcesFromCriteria($this)->execute($source);
    }

    /**
     * @param array $criteria
     * @param null $source
     * @return mixed
     */
    public function getLimits(array $criteria = [], $source = null)
    {
        $this->prepare($criteria);
        return Force::getInstance()->getResources()->getGeneral()->getLimitsFromCriteria($this)->execute($source);
    }

    /**
     * @param array $criteria
     * @param null $source
     * @return mixed
     */
    public function describe(array $criteria = [], $source = null)
    {
        $this->prepare($criteria);
        return Force::getInstance()->getResources()->getGeneral()->describeFromCriteria($this)->execute($source);
    }
}
