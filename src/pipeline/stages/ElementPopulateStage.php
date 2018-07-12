<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\pipeline\stages;

use craft\base\ElementInterface;
use craft\helpers\Json;
use flipbox\flux\helpers\TransformerHelper;
use flipbox\force\fields\Objects;
use flipbox\force\Force;
use Flipbox\Skeleton\Logger\AutoLoggerTrait;
use Flipbox\Transform\Factory;
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
class ElementPopulateStage extends BaseObject implements StageInterface
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
     * @return mixed
     * @throws \Throwable
     */
    public function __invoke($response, ElementInterface $source = null)
    {
        if ($source === null) {
            throw new InvalidArgumentException("Source must be an element.");
        }

        if (!$response instanceof ResponseInterface) {
            throw new InvalidArgumentException("Response must be an HTTP Response.");
        }

        $this->populateElement($source, $response);

        return $response;
    }

    /**
     * @param ElementInterface $element
     * @param ResponseInterface $response
     */
    protected function populateElement(ElementInterface $element, ResponseInterface $response)
    {
        $event = TransformerHelper::eventName([$this->field->object, 'populate']);
        $class = get_class($element);

        if (null === ($transformer = Force::getInstance()->getTransformers()->find($event, $class))) {
            Force::warning(
                sprintf(
                    "Populate element '%s' transformer could not be found for event '%s'",
                    $class,
                    $event
                ),
                __METHOD__
            );

            return;
        }

        Force::info(
            sprintf(
                "Populate element '%s' with transformer event '%s'",
                $class,
                $event
            ),
            __METHOD__
        );

        Factory::item(
            $transformer,
            Json::decodeIfJson(
                $response->getBody()->getContents()
            ),
            [],
            ['source' => $element, 'field' => $this->field]
        );
    }
}
