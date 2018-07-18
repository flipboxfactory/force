<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\db;

use flipbox\craft\integration\db\IntegrationAssociationQuery;
use flipbox\force\Force;
use flipbox\force\records\ObjectAssociation;

/**
 * @method ObjectAssociation[] getCachedResult()
 */
class ObjectAssociationQuery extends IntegrationAssociationQuery
{
    /**
     * @inheritdoc
     * @throws /\Throwable
     */
    public function __construct($modelClass, $config = [])
    {
        Force::getInstance()->getObjectAssociations()->ensureTableExists();
        parent::__construct($modelClass, $config);
    }
}
