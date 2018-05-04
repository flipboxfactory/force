<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\transformers\collections;

use flipbox\force\services\resources\SObject;
use flipbox\force\transformers\DynamicModelError;
use flipbox\force\transformers\DynamicModelSuccess;
use flipbox\force\transformers\elements\SObjectId;
use flipbox\force\transformers\elements\SObjectPayload;
use Flipbox\Salesforce\Transformers\Collections\TransformerCollectionInterface;
use Flipbox\Salesforce\Transformers\Collections\TransformerCollectionTrait;
use yii\base\BaseObject;

class TransformerCollection extends BaseObject implements TransformerCollectionInterface
{
    use TransformerCollectionTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->transformers = [
            TransformerCollectionInterface::SUCCESS_KEY => DynamicModelSuccess::class,
            TransformerCollectionInterface::ERROR_KEY => DynamicModelError::class,
            SObject::ID_TRANSFORMER_KEY => SObjectId::class,
            SObject::PAYLOAD_TRANSFORMER_KEY => SObjectPayload::class
        ];
    }
}
