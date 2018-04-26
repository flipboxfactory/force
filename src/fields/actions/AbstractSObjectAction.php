<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\fields\actions;

use craft\base\ElementInterface;
use craft\base\SavableComponent;
use flipbox\force\fields\SObjects;

abstract class AbstractSObjectAction extends SavableComponent implements SObjectActionInterface
{
    /**
     * The message that should be displayed to the user after the action is performed.
     *
     * @var string
     */
    private $message;

    /**
     * @inheritdoc
     */
    public static function isDestructive(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return static::displayName();
    }

    /**
     * @inheritdoc
     */
    public function getTriggerHtml()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationMessage()
    {
    }

    /**
     * @inheritdoc
     */
    public function performAction(SObjects $field, ElementInterface $element): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets the message that should be displayed to the user after the action is performed.
     *
     * @param string $message The message that should be displayed to the user after the action is performed.
     */
    protected function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            [
                'message'
            ]
        );
    }
}
