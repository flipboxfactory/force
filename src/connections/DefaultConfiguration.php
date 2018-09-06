<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\connections;

use Craft;
use flipbox\force\records\Connection;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class DefaultConfiguration implements ConnectionConfigurationInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @inheritdoc
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('force', 'Default');
    }

    /**
     * @return bool
     */
    public function process(): bool
    {
        return $this->connection->save();
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): string
    {
        return '';
    }
}
