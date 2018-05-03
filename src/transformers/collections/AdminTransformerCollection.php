<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers\collections;

use craft\helpers\ArrayHelper;
use flipbox\force\services\resources\SObject;
use flipbox\force\transformers\elements\SObjectId;
use flipbox\force\transformers\elements\SObjectPayload;
use flipbox\force\transformers\ErrorToDynamicModel;
use flipbox\force\transformers\ResponseToDynamicModel;
use Flipbox\Salesforce\Transformers\Collections\TransformerCollection;
use Flipbox\Salesforce\Transformers\Collections\TransformerCollectionInterface;

class AdminTransformerCollection extends TransformerCollection
{
    /**
     * @inheritdoc
     */
    public function __construct($transformers = [])
    {
        parent::__construct($this->transformers($transformers));
    }

    /**
     * @param $transformers
     * @return $this
     */
    public function setTransformers($transformers)
    {
        foreach ($this->ensureArray($transformers) as $key => $transformer) {
            $this->addTransformer($key, $transformer);
        }

        return $this;
    }

    /**
     * Merge transformer overrides w/ default transformer configurations
     *
     * @param array $transformers
     * @return array
     */
    private function transformers($transformers = []): array
    {
        $allTransformers = [
            TransformerCollectionInterface::ERROR_KEY => ErrorToDynamicModel::class,
            TransformerCollectionInterface::SUCCESS_KEY => ResponseToDynamicModel::class,
            SObject::ID_TRANSFORMER_KEY => SObjectId::class,
            SObject::PAYLOAD_TRANSFORMER_KEY => SObjectPayload::class
        ];

        if (empty($transformers)) {
            return $allTransformers;
        }

        if (!is_array($transformers)) {
            $transformers = [$transformers];
        }

        return ArrayHelper::merge(
            $allTransformers,
            $transformers
        );
    }
}
