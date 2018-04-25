<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\pipeline\stages;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\Json;
use flipbox\force\fields\SObjects;
use Flipbox\Skeleton\Logger\AutoLoggerTrait;
use League\Pipeline\StageInterface;
use Psr\Log\InvalidArgumentException;
use yii\base\BaseObject;

/**
 * This stage is intended to associate newly created Salesforce Objects to Craft Elements.
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ElementSaveStage extends BaseObject implements StageInterface
{
    use AutoLoggerTrait,
        traits\SObjectIdTrait;

    /**
     * @var SObjects
     */
    private $field;

    /**
     * ElementAssociationStage constructor.
     * @param SObjects $field
     * @param array $config
     */
    public function __construct(SObjects $field, $config = [])
    {
        $this->field = $field;
        parent::__construct($config);
    }

    /**
     * @param mixed $payload
     * @param ElementInterface|null $source
     * @return mixed
     * @throws \Throwable
     */
    public function __invoke($payload, ElementInterface $source = null)
    {
        /** @var Element $source */

        if ($source === null) {
            throw new InvalidArgumentException("Source must be an element.");
        }

        if (false === $this->save($source)) {
            throw new InvalidArgumentException(sprintf(
                "Unable to perform save: %s",
                (string)Json::encode($source->getErrors())
            ));
        }

        return $payload;
    }

    /**
     * @param ElementInterface $element
     * @return bool
     * @throws \Throwable
     */
    protected function save(ElementInterface $element): bool
    {
        return Craft::$app->getElements()->saveElement($element);
    }
}
