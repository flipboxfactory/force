<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/salesforce/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/salesforce
 */

namespace flipbox\force\transformers\error\object;

use flipbox\force\helpers\ErrorHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait DefinitionTrait
{
    /**
     * @param string $errorMessage
     * @param string $errorCode
     * @param array $fields
     * @return array
     */
    protected function interpretError(string $errorMessage, string $errorCode, array $fields = []): array
    {
        $errorKeys = ($fields ?: $errorCode);

        switch ($errorCode) {
            // error message looks similar to: No such column 'Foo' on object of type Bar
            case 'INVALID_FIELD':
                $errorKeys = ErrorHelper::getFieldNameFromMessage($errorMessage);
                break;

            case 'REQUIRED_FIELD_MISSING':
                $errorKeys = ErrorHelper::getFieldNamesFromRequiredMessage($errorMessage);
                break;

            case 'FIELD_CUSTOM_VALIDATION_EXCEPTION':
                if (empty($fields)) {
                    $errorKeys = $errorCode;
                }
        }

        return [$errorKeys, $errorMessage, $errorCode];
    }
}
