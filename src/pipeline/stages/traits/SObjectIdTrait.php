<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\pipeline\stages\traits;

trait SObjectIdTrait
{
    /**
     * @param $payload
     * @return string|null
     */
    protected function getSobjectIdFromPayload($payload)
    {
        if (is_string($payload)) {
            return $payload;
        }

        $id = $payload['Id'] ?? ($payload['id'] ?? null);

        return $id ? (string)$id : null;
    }
}
