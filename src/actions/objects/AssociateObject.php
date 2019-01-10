<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\actions\objects;

use flipbox\craft\integration\actions\objects\AssociateObject as AssociateIntegration;
use flipbox\craft\integration\records\IntegrationAssociation;
use flipbox\craft\salesforce\fields\Objects;
use Flipbox\Salesforce\Resources\SObject;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class AssociateObject extends AssociateIntegration
{
    /**
     * @param IntegrationAssociation $record
     * @return bool
     * @throws \Exception
     */
    protected function validate(
        IntegrationAssociation $record
    ): bool {
        $field = $record->getField();
        if (!$field instanceof Objects) {
            return false;
        }

        /** @var ResponseInterface $response */
        $response = SObject::read(
            $field->getConnection(),
            $field->getCache(),
            $field->object,
            $record->objectId
        );

        return $response->getStatusCode() >= 200 && $response->getStatusCode() <= 299;
    }
}
