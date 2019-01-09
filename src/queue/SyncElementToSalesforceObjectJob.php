<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\queue;

use craft\base\Element;
use craft\base\ElementInterface;
use flipbox\craft\ember\helpers\SiteHelper;
use flipbox\craft\integration\queries\IntegrationAssociationQuery;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use flipbox\force\records\ObjectAssociation;
use flipbox\force\transformers\CreateUpsertPayloadFromElement;
use flipbox\force\transformers\PopulateElementErrorsFromUpsertResponse;
use Flipbox\Salesforce\Resources\SObject;
use Psr\Http\Message\ResponseInterface;

/**
 * Sync a Craft Element to a Salesforce Object
 */
class SyncElementToSalesforceObjectJob extends AbstractSyncElementJob
{
    use ResolveObjectIdFromElementTrait;

    /**
     * @var string
     */
    public $transformer = CreateUpsertPayloadFromElement::class;

    /**
     * @param \craft\queue\QueueInterface|\yii\queue\Queue $queue
     * @return bool
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue)
    {
        return $this->syncUp(
            $this->getElement(),
            $this->getField()
        );
    }

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @return bool
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function syncUp(
        ElementInterface $element,
        Objects $field
    ): bool {

        $id = $this->resolveObjectIdFromElement($element, $field);

        $payload = [];

        if (null !== ($transformer = $this->resolveTransformer($this->transformer))) {
            $payload = call_user_func_array(
                $transformer,
                [
                    $element,
                    $field,
                    $id
                ]
            );
        }

        $response = SObject::upsert(
            $field->getConnection(),
            $field->getCache(),
            $field->object,
            $payload,
            $id
        );

        return $this->handleSyncUpResponse(
            $response,
            $element,
            $field,
            $id
        );
    }

    /**
     * @param ResponseInterface $response
     * @param ElementInterface $element
     * @param Objects $field
     * @param string|null $objectId
     * @return bool
     */
    protected function handleSyncUpResponse(
        ResponseInterface $response,
        ElementInterface $element,
        Objects $field,
        string $objectId = null
    ): bool {

        /** @var Element $element */

        if (!($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299)) {
            call_user_func_array(
                new PopulateElementErrorsFromUpsertResponse(),
                [
                    $response,
                    $element,
                    $field,
                    $objectId
                ]
            );
            return false;
        }

        //
        if (empty($objectId)) {
            /** @var IntegrationAssociationQuery $fieldValue */
            if (null === ($fieldValue = $element->{$field->handle})) {
                Force::warning("Field is not available on element.");
                return false;
            };

            $associations = $fieldValue->indexBy('objectId')->all();

            if (!array_key_exists($objectId, $associations)) {

                /** @var ObjectAssociation $recordClass */
                $recordClass = $field::recordClass();

                /** @var ObjectAssociation $association */
                $association = new $recordClass();
                $association->setField($this)
                    ->setElement($element)
                    ->setSiteId(SiteHelper::ensureSiteId($element->siteId));
                $association->objectId = $objectId;

                $associations[$objectId] = $association;

                $fieldValue->setCachedResult($associations);

                return $association->save();
            }
        }

        return true;
    }
}
