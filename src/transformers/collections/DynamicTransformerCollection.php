<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers\collections;

use flipbox\flux\helpers\TransformerHelper;
use flipbox\force\Force;
use flipbox\force\services\resources\SObject;
use flipbox\force\transformers\elements\SObjectId;
use flipbox\force\transformers\elements\SObjectPayload;
use flipbox\force\transformers\ErrorToDynamicModel;
use flipbox\force\transformers\ResponseToDynamicModel;
use Flipbox\Salesforce\Pipeline\Processors\HttpResponseProcessor;
use Flipbox\Salesforce\Transformers\Collections\TransformerCollectionInterface;
use yii\base\BaseObject;

class DynamicTransformerCollection extends BaseObject implements TransformerCollectionInterface
{
    /**
     * @var array
     */
    public $defaultTransformers = [
        HttpResponseProcessor::ERROR_KEY => ErrorToDynamicModel::class,
        HttpResponseProcessor::SUCCESS_KEY => ResponseToDynamicModel::class,
        SObject::ID_TRANSFORMER_KEY => SObjectId::class,
        SObject::PAYLOAD_TRANSFORMER_KEY => SObjectPayload::class
    ];

    /**
     * The transformer handle parts.  We'll assemble these in to a string such as 'sobject:account:response'
     *
     * @var array
     */
    protected $handle = [];

    /**
     * The resource class name.  Transformers can be registered to an object.  In this case, we'll
     * retrieve transformers that have been registered against the resources identified below.  Multiple
     * 'resource' classes can be provided; we'll iterate through each and return the first match.
     *
     * @var array
     */
    protected $resource = [];

    /**
     * @param array|string $resources
     * @return $this
     */
    public function setResource($resources = [])
    {
        if (!is_array($resources)) {
            $resources = [$resources];
        }

        foreach (array_filter($resources) as $resource) {
            $this->addResource($resource);
        }
        return $this;
    }

    /**
     * @param $resource
     * @return $this
     */
    public function addResource($resource)
    {
        if (!in_array($resource, $this->resource)) {
            $this->resource[] = $resource;
        }

        return $this;
    }

    /**
     * @param array|string $handles
     * @return $this
     */
    public function setHandle($handles = [])
    {
        if (!is_array($handles)) {
            $handles = [$handles];
        }

        foreach (array_filter($handles) as $handle) {
            $this->addHandle($handle);
        }
        return $this;
    }

    /**
     * @param $handle
     * @return $this
     */
    public function addHandle($handle)
    {
        if (!in_array($handle, $this->handle)) {
            $this->handle[] = $handle;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTransformer(string $key)
    {
        $event = TransformerHelper::eventName(array_merge($this->handle, [$key]));

        if (null === ($transformer = $this->resolveTransformer($key, $event))) {
            Force::warning(sprintf(
                "Unable to resolve transformer via event '%s' and key '%s'.",
                $event,
                $key
            ), __METHOD__);
        }

        return $transformer;
    }

    /**
     * @param string $key
     * @param string $eventName
     * @return callable|\Flipbox\Transform\Transformers\TransformerInterface|null
     */
    protected function resolveTransformer(string $key, string $eventName)
    {
        foreach ($this->resource as $class) {
            if (null !== ($transformer = Force::getInstance()->getTransformers()->find(
                    $eventName,
                    $class
                ))) {
                return $transformer;
            }
        }

        return TransformerHelper::resolve($this->defaultTransformers[$key] ?? null);
    }
}
