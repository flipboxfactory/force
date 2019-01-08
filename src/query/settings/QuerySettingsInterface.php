<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\query\settings;

use Flipbox\Salesforce\Query\QueryBuilderInterface;

interface QuerySettingsInterface
{
    /**
     * @return QueryBuilderInterface
     */
    public function getBuilder(): QueryBuilderInterface;

    /**
     * @return string
     */
    public function inputHtml(): string;
}
