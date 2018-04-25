<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\actions\queries\traits;

use Craft;
use flipbox\ember\exceptions\RecordNotFoundException;
use flipbox\force\records\Query;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait Populate
{
    /**
     * @param BaseObject $query
     * @return bool
     * @throws RecordNotFoundException
     */
    protected function ensureQuery(BaseObject $query): bool
    {
        if (!$query instanceof Query) {
            throw new RecordNotFoundException(sprintf(
                "Query must be an instance of '%s', '%s' given.",
                Query::class,
                get_class($query)
            ));
        }

        return true;
    }

    /**
     * These are the default body params that we're accepting.  You can lock down specific Client attributes this way.
     *
     * @return array
     */
    protected function validBodyParams(): array
    {
        return [
            'name',
            'handle'
        ];
    }

    /**
     * @param Query $query
     * @return Query
     */
    private function populateSettings(Query $query): Query
    {
        $query->settings = [
            'query' => $this->getQuerySettings()
        ];
        return $query;
    }

    /**
     * @return array
     */
    private function getQuerySettings(): array
    {
        $settings = Craft::$app->getRequest()->getBodyParam('settings.query', []);

        if (!is_array($settings)) {
            $settings = [$settings];
        }

        return $settings;
    }
}
