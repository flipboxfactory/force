<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\queue;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\Json;
use flipbox\craft\ember\helpers\SiteHelper;
use flipbox\craft\integration\queries\IntegrationAssociationQuery;
use flipbox\craft\salesforce\fields\Objects;
use flipbox\craft\salesforce\Force;
use flipbox\craft\salesforce\records\ObjectAssociation;
use flipbox\craft\salesforce\transformers\CreateUpsertPayloadFromElement;
use flipbox\craft\salesforce\transformers\PopulateElementErrorsFromUpsertResponse;
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
    public $transformer = [
        'class' => CreateUpsertPayloadFromElement::class,
        'action' => 'sync'
    ];

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
     * @param ElementInterface|element $element
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

        if (null === ($transformer = $this->resolveTransformer($this->transformer))) {
            $element->addError(
                $field->handle,
                Craft::t('salesforce', 'Invalid payload transformer.')
            );

            return false;
        }

        $payload = call_user_func_array(
            $transformer,
            [
                $element,
                $field,
                $id
            ]
        );

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

        if (empty($objectId)) {
            if (null === ($objectId = $this->getObjectIdFromResponse($response))) {
                Force::error("Unable to determine object id from response");
                return false;
            };

            /** @var IntegrationAssociationQuery $query */
            if (null === ($query = $element->{$field->handle})) {
                Force::warning("Field is not available on element.");
                return false;
            };

            $associations = $query->indexBy('objectId')->all();

            if (!array_key_exists($objectId, $associations)) {
                $recordClass = $field::recordClass();

                /** @var ObjectAssociation $association */
                $association = new $recordClass();
                $association->setField($field)
                    ->setElement($element)
                    ->setSiteId(SiteHelper::ensureSiteId($element->siteId));
                $association->objectId = $objectId;
                $associations[$objectId] = $association;

                $query->setCachedResult($associations);

                return $association->save();
            }
        }

        return true;
    }

    /**
     * @param ResponseInterface $response
     * @return string|null
     */
    protected function getObjectIdFromResponse(ResponseInterface $response)
    {
        $data = Json::decodeIfJson(
            $response->getBody()->getContents()
        );

        $id = $data['Id'] ?? ($data['id'] ?? null);

        return $id ? (string)$id : null;
    }
}
