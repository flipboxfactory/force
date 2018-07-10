<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\pipeline\stages;

use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\Json;
use flipbox\force\db\ObjectAssociationQuery;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use Flipbox\Skeleton\Logger\AutoLoggerTrait;
use League\Pipeline\StageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\InvalidArgumentException;
use yii\base\BaseObject;

/**
 * This stage is intended to associate newly created Salesforce Objects to Craft Elements.
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ElementAssociationStage extends BaseObject implements StageInterface
{
    use AutoLoggerTrait;

    /**
     * @var Objects
     */
    private $field;

    /**
     * ElementAssociationStage constructor.
     * @param Objects $field
     * @param array $config
     */
    public function __construct(Objects $field, $config = [])
    {
        $this->field = $field;
        parent::__construct($config);
    }

    /**
     * @param mixed $response
     * @param ElementInterface|null $source
     * @return string|null
     * @throws \Throwable
     */
    public function __invoke($response, ElementInterface $source = null)
    {
        /** @var Element $source */
        if ($source === null) {
            throw new InvalidArgumentException("Source must be an element.");
        }

        /** @var Element $source */
        if (!$response instanceof ResponseInterface) {
            throw new InvalidArgumentException(sprintf(
                "Data must be an instance of '%s'.",
                ResponseInterface::class
            ));
        }

        if (null === $source->getId()) {
            Force::error("The element must have an Id");
            return null;
        }

        if ($source->hasErrors()) {
            Force::error("The element has errors, not associating...");
            return null;
        }

        if (null === ($objectId = $this->getObjectIdFromResponse($response))) {
            Force::error(sprintf(
                "Unable to identify Force id from payload: %s",
                (string)Json::encode($response)
            ));
            return null;
        }

        if (false === $this->associate($objectId, $source)) {
            throw new InvalidArgumentException(sprintf(
                "Unable to perform save: %s",
                (string)Json::encode($source->getErrors())
            ));
        }

        Force::info(sprintf(
            "Successfully associated object '%s' to element '%s'",
            (string)$objectId,
            $source->getId()
        ));


        return $response;
    }

    /**
     * @param string $objectId
     * @param ElementInterface $element
     * @return bool
     * @throws \Throwable
     */
    protected function associate(string $objectId, ElementInterface $element): bool
    {
        /** @var Element $element */
        $fieldHandle = $this->field->handle;

        /** @var ObjectAssociationQuery $fieldValue */
        if (null === ($fieldValue = $element->{$fieldHandle})) {
            $this->warning("Field is not available on element.");
            return false;
        };

        $associations = $fieldValue->indexBy('objectId')->all();

        if (!array_key_exists($objectId, $associations)) {
            $associations[$objectId] = Force::getInstance()->getObjectAssociations()->create([
                'objectId' => $objectId,
                'elementId' => $element->getId(),
                'fieldId' => $this->field->id,
                'siteId' => $element->siteId
            ]);

            $fieldValue->setCachedResult($associations);

            return Force::getInstance()->getObjectAssociations()->save(
                $fieldValue
            );
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
