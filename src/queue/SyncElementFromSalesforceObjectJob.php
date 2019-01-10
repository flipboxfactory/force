<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\queue;

use Craft;
use craft\base\ElementInterface;
use flipbox\craft\salesforce\fields\Objects;
use flipbox\craft\salesforce\transformers\PopulateElementErrorsFromResponse;
use flipbox\craft\salesforce\transformers\PopulateElementFromResponse;
use Flipbox\Salesforce\Resources\SObject;

/**
 * Sync a Salesforce Object to a Craft Element
 */
class SyncElementFromSalesforceObjectJob extends AbstractSyncElementJob
{
    use ResolveObjectIdFromElementTrait;

    /**
     * @var string
     */
    public $transformer = [
        'class' => PopulateElementFromResponse::class,
        'action' => 'sync'
    ];

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
        return $this->syncDown(
            $this->getElement(),
            $this->getField()
        );
    }

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @return bool
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function syncDown(
        ElementInterface $element,
        Objects $field
    ): bool {
        /** @var string $id */
        if (null === ($id = $this->resolveObjectIdFromElement($element, $field))) {
            return false;
        }

        $response = SObject::read(
            $field->getConnection(),
            $field->getCache(),
            $field->object,
            $id
        );

        if (($response->getStatusCode() < 200 || $response->getStatusCode() > 300)) {
            call_user_func_array(
                new PopulateElementErrorsFromResponse(),
                [
                    $response,
                    $element,
                    $field,
                    $id
                ]
            );
            return false;
        }

        if (null !== ($transformer = $this->resolveTransformer($this->transformer))) {
            call_user_func_array(
                $transformer,
                [
                    $response,
                    $element,
                    $field,
                    $id
                ]
            );
        }

        return Craft::$app->getElements()->saveElement($element);
    }
}
