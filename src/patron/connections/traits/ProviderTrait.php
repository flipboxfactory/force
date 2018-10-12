<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\patron\connections\traits;

use Craft;
use flipbox\patron\Patron;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce;
use yii\base\InvalidArgumentException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait ProviderTrait
{
    /**
     * @var mixed
     */
    private $provider;

    /**
     * @param $provider
     * @return $this
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasProvider(): bool
    {
        return $this->findProvider() instanceof Salesforce;
    }

    /**
     * @return null|Salesforce
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function findProvider()
    {
        if ($this->provider instanceof Salesforce) {
            return $this->provider;
        }

        if ($this->provider === false) {
            return null;
        }

        return $this->provider = $this->resolveProvider($this->provider);
    }

    /**
     * @return Salesforce
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function getProvider(): Salesforce
    {
        if (null === ($provider = $this->findProvider())) {
            throw new InvalidArgumentException("Unable to resolve provider");
        }

        return $provider;
    }

    /**
     * @param $provider
     * @return Salesforce|false
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    protected function resolveProvider($provider)
    {
        if (is_numeric($provider) || is_string($provider)) {
            $provider = Patron::getInstance()->getProviders()->find($provider);
        } else {
            $provider = Craft::createObject($provider);
        }

        if (!$provider instanceof Salesforce) {
            return false;
        }

        return $provider;
    }
}
