<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\search\traits;

use craft\helpers\ArrayHelper;
use flipbox\force\search\DynamicSearchBuilder;
use flipbox\force\search\SearchBuilderInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait SearchBuilderAttributeTrait
{
    /**
     * @var SearchBuilderInterface|string
     */
    private $search = '';

    /**
     * @param array $config
     * @return SearchBuilderInterface
     */
    public function getSearch(array $config = []): SearchBuilderInterface
    {
        if (!$this->search instanceof SearchBuilderInterface) {
            $this->search = $this->createSearchBuilder($this->search);
        }

        if (!empty($config)) {
            $this->populateSearch(
                $this->search,
                $config
            );
        };

        return $this->search;
    }

    /**
     * @param $search
     */
    public function setSearch($search)
    {
        if ($search instanceof SearchBuilderInterface) {
            $this->search = $search;
            return;
        }

        $this->populateSearch(
            $this->getSearch(),
            $this->normalizeSearchConfig($search)
        );
    }

    /**
     * @param SearchBuilderInterface $search
     * @param array $config
     */
    private function populateSearch(SearchBuilderInterface $search, array $config = [])
    {
        foreach ($config as $name => $value) {
            $setter = 'set' . $name;
            if (method_exists($this, $setter)) {
                $this->$setter($value);
                continue;
            }

            if (property_exists($search, $name)) {
                $search->{$name} = $value;
                continue;
            }
        }
    }

    /**
     * @param $config
     * @return array
     */
    private function normalizeSearchConfig($config): array
    {
        if (!is_array($config)) {
            if (is_string($config)) {
                $config = ['soql' => $config];
            } else {
                $config = ArrayHelper::toArray($config, [], false);
            }
        }

        return $config;
    }

    /**
     * @param $search
     * @return SearchBuilderInterface
     */
    protected function createSearchBuilder($search): SearchBuilderInterface
    {
        if ($search instanceof SearchBuilderInterface) {
            return $search;
        }

        $builder = new DynamicSearchBuilder();

        $this->populateSearch(
            $builder,
            $this->normalizeSearchConfig($search)
        );

        return $builder;
    }
}
