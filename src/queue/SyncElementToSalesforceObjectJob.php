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
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\Exception
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
     * @throws \yii\base\InvalidConfigException
     */

    public function syncUp(
        ElementInterface $element,
        Objects $field
    ): bool
    {

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

        return $this->rawSyncUp(
            $element,
            $field,
            $payload,
            $this->resolveObjectIdFromElement($element, $field)
        );
    }

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param array $payload
     * @param string|null $id
     * @param Pipeline|null $pipeline
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function rawSyncUp(
        ElementInterface $element,
        Objects $field,
        array $payload,
        string $id = null,
        Pipeline $pipeline = null
    ): bool
    {
        $response = SObject::upsert(
            $field->getConnection(),
            $field->getCache(),
            $field->object,
            $payload,
            $id
        );

//        /** @var Element $element */
//
//        /** @var ResponseInterface $response */
//        $response = $this->rawHttpUpsertRelay(
//            $field->object,
//            $payload,
//            $id,
//            $field->getConnection(),
//            $field->getCache()
//        )();

        return $this->handleSyncUpResponse(
            $response,
            $element,
            $field,
            $id,
            $pipeline
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
    ): bool
    {

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

    /**
     * @param ResponseInterface $response
     * @param ElementInterface $element
     */
    protected function handleResponseErrors(ResponseInterface $response, ElementInterface $element)
    {
        /** @var Element $element */

        $data = Json::decodeIfJson(
            $response->getBody()->getContents()
        );

        $errors = (array)Factory::item(
            new Interpret(),
            $data
        );

        $errors = array_filter($errors);

        if (empty($errors)) {
            $element->addErrors($errors);
        }
    }


}
