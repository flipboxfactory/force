<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\patron\connections\traits;

use flipbox\patron\Patron;
use Flipbox\Salesforce\Helpers\ErrorHelper;
use Flipbox\Skeleton\Helpers\JsonHelper;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait AccessTokenAuthorizationTrait
{
    use AccessTokenTrait;

    /**
     * @inheritdoc
     */
    public function prepareAuthorizationRequest(
        RequestInterface $request
    ): RequestInterface {
        return $this->addAuthorizationHeader($request);
    }

    /**
     * Add an Authorization Bearer header to the request
     *
     * @param RequestInterface $request
     * @return RequestInterface
     */
    protected function addAuthorizationHeader(RequestInterface $request): RequestInterface
    {
        return $request->withHeader(
            'Authorization',
            'Bearer ' . (string)$this->getAccessToken()
        );
    }

    /**
     * @inheritdoc
     */
    public function handleAuthorizationResponse(
        ResponseInterface $response,
        RequestInterface $request,
        callable $runner
    ): ResponseInterface {

        if ($this->responseIsExpiredToken($response)) {
            $response = $this->refreshAndRetry(
                $request,
                $response,
                $runner
            );
        }

        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @return bool
     */
    protected function responseIsExpiredToken(ResponseInterface $response): bool
    {
        if ($response->getStatusCode() !== 401) {
            return false;
        }

        $data = JsonHelper::decodeIfJson(
            $response->getBody()->getContents()
        );

        return ErrorHelper::hasSessionExpired($data);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return mixed
     */
    protected function refreshAndRetry(RequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $refreshToken = $this->getProvider()->getAccessToken('refresh_token', [
            'refresh_token' => $this->getAccessToken()->getRefreshToken()
        ]);

        $this->saveRefreshToken(
            $this->getAccessToken(),
            $refreshToken
        );

        $this->setAccessToken($refreshToken);

        return $next(
            $this->addAuthorizationHeader($request),
            $response
        );
    }

    /**
     * @param AccessToken $accessToken
     * @param AccessToken $refreshToken
     * @return bool
     */
    protected function saveRefreshToken(AccessToken $accessToken, AccessToken $refreshToken): bool
    {
        $model = Patron::getInstance()->manageTokens()->get($accessToken);
        $model->accessToken = $refreshToken->getToken();
        return $model->save();
    }
}
