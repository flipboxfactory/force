<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\patron\connections;

use Flipbox\Salesforce\Connections\ConnectionInterface;
use Psr\Http\Message\RequestInterface;
use yii\base\BaseObject;
use Zend\Diactoros\Uri;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class AccessTokenConnection extends AbstractAccessTokenConnection
{
    /**
     * The Salesforce API Version (ex: v41.0)
     * @var string
     */
    public $version;

    /**
     * @inheritdoc
     */
    public function getResourceUrl(): string
    {
        return $this->getProvider()->getDomain() .
            '/services/data' .
            (!empty($this->version) ? ('/' . $this->version) : '');
    }
}
