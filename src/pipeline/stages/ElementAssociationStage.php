<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\pipeline\stages;

use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\Json;
use flipbox\force\db\SObjectFieldQuery;
use flipbox\force\fields\SObjects;
use flipbox\force\Force;
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
class ElementAssociationStage extends BaseObject implements StageInterface
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
     * @return string|null
     * @throws \Throwable
     */
    public function __invoke($payload, ElementInterface $source = null)
    {
        /** @var Element $source */
        if ($source === null) {
            throw new InvalidArgumentException("Source must be an element.");
        }

        if (null === $source->getId()) {
            Force::error("The element must have an Id");
            return null;
        }

        if (null === ($sObjectId = $this->getSobjectIdFromPayload($payload))) {
            Force::error(sprintf(
                "Unable to identify sObjectId from payload: %s",
                (string)Json::encode($payload)
            ));
            return null;
        }

        if (false === $this->associate($sObjectId, $source)) {
            throw new InvalidArgumentException(sprintf(
                "Unable to perform save: %s",
                (string)Json::encode($source->getErrors())
            ));
        }


        return $payload;
    }

    /**
     * @param string $sobjectId
     * @param ElementInterface $element
     * @return bool
     * @throws \Throwable
     */
    protected function associate(string $sobjectId, ElementInterface $element): bool
    {
        /** @var Element $element */
        $fieldHandle = $this->field->handle;

        /** @var SObjectFieldQuery $fieldValue */
        if (null === ($fieldValue = $element->{$fieldHandle})) {
            $this->warning("Field is not available on element.");
            return false;
        };

        if (null === ($criteria = $fieldValue->sObjectId($sobjectId)->one())) {
            $criteria = Force::getInstance()->getResources()->getSObject()->getCriteria([
                'sObject' => $this->field->sObject,
                'id' => $sobjectId
            ]);
        }

        return Force::getInstance()->getSObjectsField()->saveAssociation(
            $this->field,
            $criteria,
            $element
        );
    }
}
