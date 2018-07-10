<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\traits;

use Craft;
use craft\base\ElementInterface;
use flipbox\force\db\ObjectAssociationQuery;
use flipbox\force\fields\Objects;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait FieldResolverTrait
{
    /**
     * @param string $field
     * @return Objects
     * @throws HttpException
     */
    protected function resolveField(string $field): Objects
    {
        $field = is_numeric($field) ?
            Craft::$app->getFields()->getFieldbyId($field) :
            Craft::$app->getFields()->getFieldByHandle($field);

        /** @var Objects $field */

        if (!$field instanceof Objects) {
            throw new HttpException(400, sprintf(
                "Field must be an instance of '%s', '%s' given.",
                Objects::class,
                get_class($field)
            ));
        }

        return $field;
    }

    /**
     * @param Objects $field
     * @param ElementInterface $element
     * @param string $id
     * @return array|mixed|null|\yii\base\BaseObject
     * @throws HttpException
     */
    protected function resolveRecord(Objects $field, ElementInterface $element, string $id)
    {
        /** @var ObjectAssociationQuery $query */
        if (null === ($query = $element->getFieldValue($field->handle))) {
            throw new HttpException(400, 'Field is not associated to element');
        }

        if (null === ($record = $query->objectId($id)->one())) {
            throw new HttpException(400, 'Invalid value');
        };

        return $record;
    }
}
