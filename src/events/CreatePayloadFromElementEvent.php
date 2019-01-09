<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\events;

use craft\base\ElementInterface;
use craft\helpers\StringHelper;
use yii\base\Event;

/**
 * @param ElementInterface $sender
 */
class CreatePayloadFromElementEvent extends Event
{
    /**
     * @var array
     */
    public $payload = [];

    /**
     * @param string $object
     * @param string|null $action
     * @return string
     */
    public static function eventName(
        string $object,
        string $action = null
    ): string {
        $name = array_filter([
            'payload',
            $object,
            $action
        ]);

        return StringHelper::toLowerCase(
            StringHelper::toString($name, ':')
        );
    }
}
