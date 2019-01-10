<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\queue;

use Craft;
use craft\base\ElementInterface;
use craft\queue\BaseJob;
use flipbox\craft\salesforce\fields\Objects;
use yii\base\InvalidConfigException;

/**
 * Sync a Salesforce Object to a Craft Element
 */
abstract class AbstractSyncElementJob extends BaseJob
{
    /**
     * @var Objects
     */
    public $field;

    /**
     * @var ElementInterface
     */
    public $element;

    /**
     * @return ElementInterface
     * @throws InvalidConfigException
     */
    protected function getElement(): ElementInterface
    {
        if ($this->isElementInstance($this->element)) {
            return $this->element;
        }

        if (is_numeric($this->element)) {
            $element = Craft::$app->getElements()->getElementById($this->element);
            if ($this->isElementInstance($element)) {
                $this->element = $element;
                return $this->element;
            }
        }

        if (is_string($this->element)) {
            $element = Craft::$app->getElements()->getElementByUri($this->element);
            if ($this->isElementInstance($element)) {
                $this->element = $element;
                return $this->element;
            }
        }

        throw new InvalidConfigException("Unable to resolve element");
    }

    /**
     * @param null $element
     * @return bool
     */
    private function isElementInstance($element = null)
    {
        return $element instanceof ElementInterface;
    }

    /**
     * @return Objects
     * @throws InvalidConfigException
     */
    protected function getField(): Objects
    {
        if ($this->isFieldInstance($this->field)) {
            return $this->field;
        }

        if (is_numeric($this->field)) {
            $field = Craft::$app->getFields()->getFieldById($this->field);
            if ($this->isFieldInstance($field)) {
                $this->field = $field;
                return $this->field;
            }
        }

        if (is_string($this->field)) {
            $field = Craft::$app->getFields()->getFieldByHandle($this->field);
            if ($this->isFieldInstance($field)) {
                $this->field = $field;
                return $this->field;
            }
        }

        throw new InvalidConfigException("Unable to resolve field");
    }

    /**
     * @param null $field
     * @return bool
     */
    private function isFieldInstance($field = null)
    {
        return $field instanceof Objects;
    }


    /**
     * @param $transformer
     * @return bool
     */
    protected function isTransformerClass($transformer): bool
    {
        return is_string($transformer) &&
            class_exists($transformer) &&
            (
                method_exists($transformer, '__invoke') ||
                is_callable([$transformer, '__invoke'])
            );
    }

    /**
     * @param $transformer
     * @return bool
     */
    protected function isTransformerClassArray($transformer): bool
    {
        if (!is_array($transformer)) {
            false;
        }

        return $this->isTransformerClass($transformer['class'] ?? null);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @param $transformer
     * @return callable|null
     */
    protected function resolveTransformer($transformer)
    {
        if (is_callable($transformer)) {
            return $transformer;
        }

        if ($this->isTransformerClass($transformer)) {
            return new $transformer();
        }

        if ($this->isTransformerClassArray($transformer)) {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            /** @noinspection PhpUnhandledExceptionInspection */
            return Craft::createObject($transformer);
        }

        return null;
    }
}
