<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\patron\connections;

/**
 * A Salesforce connection which consists of an OAuth2 provider along with the specified API version to use.
 *
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
        return $this->getInstanceUrl() .
            '/services/data' .
            (!empty($this->version) ? ('/' . $this->version) : '');
    }
}
