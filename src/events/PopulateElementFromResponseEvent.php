<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\events;

use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\StringHelper;
use flipbox\craft\salesforce\fields\Objects;
use Psr\Http\Message\ResponseInterface;
use yii\base\Event;

/**
 * @property ElementInterface|Element $sender
 */
class PopulateElementFromResponseEvent extends Event
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var Objects
     */
    private $field;

    /**
     * @var string|null
     */
    public $objectId;

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param Objects $field
     * @return $this
     */
    public function setField(Objects $field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return Objects
     */
    public function getField(): Objects
    {
        return $this->field;
    }

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
            'populate',
            $object,
            $action
        ]);

        return StringHelper::toLowerCase(
            StringHelper::toString($name, ':')
        );
    }
}
