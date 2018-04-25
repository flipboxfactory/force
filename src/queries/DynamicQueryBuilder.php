<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\queries;

use Craft;
use Flipbox\Salesforce\Query\AbstractQueryBuilder;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class DynamicQueryBuilder extends AbstractQueryBuilder
{
    use traits\DynamicVariablesAttribute;

    /**
     * The soql query
     *
     * @var string
     */
    public $soql;

    /**
     * @inheritdoc
     */
    public function build(): string
    {
        return $this->prepareSoql($this->soql);
    }

    /**
     * @param string $soql
     * @return string
     */
    private function prepareSoql(string $soql): string
    {
        return Craft::$app->getView()->renderString(
            $soql,
            $this->getVariables()
        );
    }

    /**
     * @return array
     */
    public function toConfig(): array
    {
        return array_merge(
            parent::toConfig(),
            [
                'soql' => $this->soql,
                'variables' => $this->getVariables()
            ]
        );
    }
}
