<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\objects;

use flipbox\craft\integration\actions\objects\Associate as AssociateIntegration;
use flipbox\craft\integration\records\IntegrationAssociation;
use flipbox\craft\integration\services\IntegrationAssociations;
use flipbox\force\Force;
use flipbox\force\services\ObjectAssociations;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Associate extends AssociateIntegration
{
    /**
     * @inheritdoc
     * @return ObjectAssociations
     */
    protected function associationService(): IntegrationAssociations
    {
        return Force::getInstance()->getObjectAssociations();
    }

    /**
     * @param IntegrationAssociation $record
     * @return bool
     * @throws \Exception
     */
    protected function validate(
        IntegrationAssociation $record
    ): bool {

        if (null === ($fieldId = $record->fieldId)) {
            return false;
        }

        if (null === ($field = Force::getInstance()->getObjectsField()->findById($fieldId))) {
            return false;
        }

        /** @var ResponseInterface $response */
        $response = Force::getInstance()->getResources()->getObject()->rawHttpRead(
            $field->object,
            $record->objectId,
            $field->getConnection(),
            $field->getCache()
        );

        return $response->getStatusCode() >= 200 && $response->getStatusCode() <= 299;
    }
}
