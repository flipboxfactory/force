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
     * @return Salesforce
     * @throws \yii\base\InvalidConfigException
     */
    public function getProvider(): Salesforce
    {
        if ($this->provider instanceof Salesforce) {
            return $this->provider;
        }

        return $this->provider = $this->resolveProvider($this->provider);
    }

    /**
     * @param $provider
     * @return Salesforce
     * @throws \yii\base\InvalidConfigException
     */
    protected function resolveProvider($provider): Salesforce
    {
        if (is_numeric($provider) || is_string($provider)) {
            $provider = Patron::getInstance()->getProviders()->get($provider);
        } else {
            $provider = Craft::createObject($provider);
        }

        if (!$provider instanceof Salesforce) {
            throw new InvalidArgumentException("Unable to resolve provider");
        }

        return $provider;
    }
}
