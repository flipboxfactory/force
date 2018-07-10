<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\widgets;

use Craft;
use craft\base\ElementInterface;
use flipbox\force\actions\traits\FieldResolverTrait;
use flipbox\force\cp\actions\sync\AbstractSyncFrom;
use flipbox\force\db\ObjectAssociationQuery;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SyncFrom extends AbstractSyncFrom
{
    use FieldResolverTrait;

    /**
     * @param string $id
     * @param string $field
     * @param string $elementType
     * @return mixed
     * @throws HttpException
     * @throws \Exception
     */
    public function run(string $id, string $field, string $elementType)
    {
        $field = $this->resolveField($field);

        /** @var ElementInterface $element */
        $element = $this->resolveElement($field, $id, $elementType);

        /** @var ObjectAssociationQuery $query */
        if (null === ($query = $element->getFieldValue($field->handle))) {
            throw new HttpException(400, 'Invalid value');
        }

        return $this->runInternal($element, $field);
    }

    /**
     * @param Objects $field
     * @param string $id
     * @param string $elementType
     * @return ElementInterface
     */
    private function resolveElement(
        Objects $field,
        string $id,
        string $elementType
    ): ElementInterface {
        $elementId = Force::getInstance()->getObjectAssociations()->getQuery([
            'select' => ['elementId'],
            'fieldId' => $field->id,
            'objectId' => $id
        ])->scalar();

        $element = null;

        if ($elementId !== null) {
            $element = Craft::$app->getElements()->getElementById($elementId, $elementType);
        }

        if ($element === null) {
            $element = Craft::$app->getElements()->createElement($elementType);
        }

        return $element;
    }
}
