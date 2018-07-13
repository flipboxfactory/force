<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\pipeline;

use flipbox\force\Force;
use flipbox\force\pipeline\pipelines\HttpPipeline;
use flipbox\force\pipeline\stages\TransformerCollectionStage;
use flipbox\force\transformers\collections\TransformerCollectionInterface;
use Flipbox\Pipeline\Builders\BuilderTrait;
use Flipbox\Skeleton\Object\AbstractObject;
use League\Pipeline\PipelineBuilderInterface;
use Psr\Log\LoggerInterface;

/**
 * A Relay pipeline builder intended to make building the Relay and Pipeline easier.
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method HttpPipeline build()
 */
class Resource extends AbstractObject implements PipelineBuilderInterface
{
    use BuilderTrait;

    /**
     * @var callable
     */
    protected $relay;

    /**
     * @var TransformerCollectionInterface|null
     */
    protected $transformer;

    /**
     * @param callable $relay
     * @param TransformerCollectionInterface|null $transformer
     * @param LoggerInterface|null $logger
     * @param array $config
     */
    public function __construct(
        callable $relay,
        TransformerCollectionInterface $transformer = null,
        LoggerInterface $logger = null,
        array $config = []
    ) {

        $this->setLogger($logger ?: Force::getInstance()->getPsrLogger());
        $this->relay = $relay;
        $this->transformer = $transformer;

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function createPipeline(array $config = []): HttpPipeline
    {
        $pipeline = new HttpPipeline(
            function () {
                return call_user_func($this->relay);
            },
            $this->createTransformerStage($this->transformer),
            $config
        );

        $pipeline->setLogger($this->getLogger());

        return $pipeline;
    }

    /**
     * @param null $source
     * @return mixed
     */
    public function execute(array $extra = [])
    {
        // Resources do not pass a payload ... but they can pass a source, so that why this may look funny
        return call_user_func_array($this->build(), [null, $extra]);
    }

    /**
     * @param array $extra
     * @return mixed
     */
    public function __invoke(array $extra = [])
    {
        return $this->execute($extra);
    }

    /**
     * @param TransformerCollectionInterface|null $transformer
     * @return TransformerCollectionStage|null
     */
    private function createTransformerStage(
        TransformerCollectionInterface $transformer = null
    ) {
        if ($transformer === null) {
            return null;
        }

        return new TransformerCollectionStage($transformer);
    }
}
