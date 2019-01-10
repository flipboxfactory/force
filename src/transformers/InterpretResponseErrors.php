<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/salesforce/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/salesforce
 */

namespace flipbox\craft\salesforce\transformers;

use craft\helpers\ArrayHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class InterpretResponseErrors
{
    /**
     * @inheritdoc
     */
    public function __invoke(array $data): array
    {
        if ($data === null) {
            return [
                'error' => 'An unknown error occurred.'
            ];
        }
        return $this->normalizeErrors($data);
    }

    /**
     * @param array $errors
     * @return array
     */
    public function normalizeErrors(array $errors): array
    {
        $preparedErrors = [];
        foreach ($errors as $error) {
            list($errorKeys, $errorMessage) = $this->prepareError($error);
            if (!is_array($errorKeys)) {
                $errorKeys = [$errorKeys];
            }
            foreach ($errorKeys as $errorKey) {
                $preparedErrors[$errorKey][] = $errorMessage;
            }
        }
        return $preparedErrors;
    }

    /**
     * @param array $error
     * @return array
     */
    protected function prepareError(array $error): array
    {
        return $this->interpretError(
            ArrayHelper::getValue($error, 'message'),
            ArrayHelper::getValue($error, 'errorCode'),
            ArrayHelper::getValue($error, 'fields', [])
        );
    }

    /**
     * @param $errorMessage
     * @param $errorCode
     * @param array $fields
     * @return array
     */
    protected function interpretError(string $errorMessage, string $errorCode, array $fields = []): array
    {
        return [($fields ?: $errorCode), $errorMessage, $errorCode];
    }
}
