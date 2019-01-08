<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipboxfactory/craft-sortable-associations/blob/master/LICENSE
 * @link       https://github.com/flipboxfactory/craft-sortable-associations
 */

namespace flipbox\force\queries;

use flipbox\craft\ember\queries\ActiveQuery;
use flipbox\craft\ember\queries\AuditAttributesTrait;
use flipbox\craft\sortable\associations\records\SortableAssociationInterface;

/**
 * @method SortableAssociationInterface[] getCachedResult()
 *
 * deprecated
 */
class ActiveSOQLQuery extends ActiveQuery
{
    use AuditAttributesTrait;

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public function prepare($builder)
    {
        $this->applyAuditAttributeConditions();

        return parent::prepare($builder);
    }

}
