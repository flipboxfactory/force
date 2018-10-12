<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\patron\connections;

use Craft;
use flipbox\force\connections\ConnectionInterface;
use Psr\Http\Message\RequestInterface;
use yii\base\BaseObject;
use Zend\Diactoros\Uri;

/**
 * An abstract Access Token class that can be used to build your own.
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class AbstractAccessTokenConnection extends BaseObject implements ConnectionInterface
{
    use traits\AccessTokenAuthorizationTrait,
        traits\ProviderTrait;

    /**
     * @return string
     */
    abstract protected function getResourceUrl(): string;

    /**
     * @inheritdoc
     */
    public function getInstanceUrl(): string
    {
        if (!$this->hasProvider()) {
            return Craft::t('force', 'INVALID PROVIDER');
        }

        return rtrim($this->getProvider()->getDomain(), '/');
    }

    /**
     * @param RequestInterface $request
     * @return RequestInterface
     */
    public function prepareInstanceRequest(RequestInterface $request): RequestInterface
    {
        $request = $request->withUri(
            new Uri($this->getResourceUrl())
        );

        foreach ($this->getProvider()->getHeaders() as $key => $value) {
            $request = $request->withAddedHeader($key, $value);
        }

        return $request;
    }
}
