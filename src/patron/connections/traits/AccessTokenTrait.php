<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\patron\connections\traits;

use flipbox\patron\Patron;
use League\OAuth2\Client\Token\AccessToken;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait AccessTokenTrait
{
    /**
     * @var AccessToken|null
     */
    private $accessToken;

    /**
     * @return Salesforce
     */
    abstract public function getProvider(): Salesforce;

    /**
     * @param AccessToken $accessToken
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return AccessToken
     */
    public function getAccessToken(): AccessToken
    {
        if ($this->accessToken instanceof AccessToken) {
            return $this->accessToken;
        }

        return $this->accessToken = Patron::getInstance()->getTokens()->get($this->getProvider());
    }
}
