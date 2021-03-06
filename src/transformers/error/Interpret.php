<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/salesforce/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/salesforce
 */

namespace flipbox\force\transformers\error;

use Flipbox\Skeleton\Helpers\ArrayHelper;
use Flipbox\Transform\Traits\MapperTrait;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Interpret
{
    use MapperTrait;

    /**
     * @inheritdoc
     */
    public function __invoke(array $data)
    {
        if ($data === null) {
            return [
                'error' => 'An unknown error occurred.'
            ];
        }

        return $this->mapFrom(
            $this->normalizeErrors($data)
        );
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
