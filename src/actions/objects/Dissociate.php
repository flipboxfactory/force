<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\objects;

use flipbox\ember\helpers\SiteHelper;
use flipbox\force\Force;
use flipbox\force\records\ObjectAssociation;
use yii\base\Model;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Dissociate extends AbstractAssociationAction
{
    /**
     * @param string $field
     * @param string $element
     * @param string $objectId
     * @param int|null $siteId
     * @return mixed
     * @throws HttpException
     */
    public function run(
        string $field,
        string $element,
        string $objectId,
        int $siteId = null
    ) {
        // Resolve Field
        $field = $this->resolveField($field);

        // Resolve Element
        $element = $this->resolveElement($element);

        return $this->runInternal(Force::getInstance()->getObjectAssociations()->create([
            'objectId' => $objectId,
            'elementId' => $element->getId(),
            'fieldId' => $field->id,
            'siteId' => SiteHelper::ensureSiteId($siteId ?: $element->siteId),
        ]));
    }

    /**
     * @inheritdoc
     * @param ObjectAssociation $model
     * @throws \flipbox\ember\exceptions\RecordNotFoundException
     * @throws \yii\db\Exception
     */
    protected function performAction(Model $model): bool
    {
        if (true === $this->ensureAssociation($model)) {
            return Force::getInstance()->getObjectAssociations()->dissociate(
                $model
            );
        }

        return false;
    }
}
