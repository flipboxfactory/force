<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\transformers;

use flipbox\craft\salesforce\helpers\ErrorHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait UpsertErrorInterpreterTrait
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
