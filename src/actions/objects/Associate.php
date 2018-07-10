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
use Psr\Http\Message\ResponseInterface;
use yii\base\Model;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Associate extends AbstractAssociationAction
{
    /**
     * Validate that the HubSpot Object exists prior to associating
     *
     * @var bool
     */
    public $validate = true;

    /**
     * @param string $field
     * @param string $element
     * @param string $newObjectId
     * @param string|null $objectId
     * @param int|null $siteId
     * @param int|null $sortOrder
     * @return Model
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\web\HttpException
     */
    public function run(
        string $field,
        string $element,
        string $newObjectId,
        string $objectId = null,
        int $siteId = null,
        int $sortOrder = null
    ) {
        // Resolve Field
        $field = $this->resolveField($field);

        // Resolve Element
        $element = $this->resolveElement($element);

        // Find existing?
        if (!empty($objectId)) {
            $association = Force::getInstance()->getObjectAssociations()->getByCondition([
                'objectId' => $objectId,
                'elementId' => $element->getId(),
                'fieldId' => $field->id,
                'siteId' => SiteHelper::ensureSiteId($siteId ?: $element->siteId),
            ]);
        } else {
            $association = Force::getInstance()->getObjectAssociations()->create([
                'elementId' => $element->getId(),
                'fieldId' => $field->id,
                'siteId' => SiteHelper::ensureSiteId($siteId ?: $element->siteId),
            ]);
        }

        $association->objectId = $newObjectId;
        $association->sortOrder = $sortOrder;

        return $this->runInternal($association);
    }

    /**
     * @inheritdoc
     * @param ObjectAssociation $model
     * @throws \flipbox\ember\exceptions\RecordNotFoundException
     * @throws \Exception
     */
    protected function performAction(Model $model): bool
    {
        if (true === $this->ensureAssociation($model)) {
            if ($this->validate === true && !$this->validate($model)) {
                return false;
            }

            return Force::getInstance()->getObjectAssociations()->associate(
                $model
            );
        }

        return false;
    }

    /**
     * @param ObjectAssociation $record
     * @return bool
     * @throws \Exception
     */
    protected function validate(
        ObjectAssociation $record
    ): bool {

        if (null === ($fieldId = $record->fieldId)) {
            return false;
        }

        if (null === ($field = Force::getInstance()->getObjectsField()->findById($fieldId))) {
            return false;
        }

        /** @var ResponseInterface $response */
        $response = Force::getInstance()->getResources()->getObject()->rawHttpRead(
            $field->object,
            $record->objectId,
            $field->getConnection(),
            $field->getCache()
        );

        return $response->getStatusCode() >= 200 && $response->getStatusCode() <= 299;
    }
}
