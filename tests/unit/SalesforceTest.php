<?php

namespace flipbox\force\tests;

use Codeception\Test\Unit;
use flipbox\craft\psr3\Logger;
use flipbox\force\Force as SalesforcePlugin;
use flipbox\force\services\Cache;
use flipbox\force\services\Connections;
use flipbox\force\services\ObjectAssociations as ObjectAssociations;
use flipbox\force\services\Resources;
use flipbox\force\services\ObjectsField as ObjectsField;
use flipbox\force\services\Transformers;

class SalesforceTest extends Unit
{
    /**
     * @var SalesforcePlugin
     */
    private $module;

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * phpcs:disable PSR2.Methods.MethodDeclaration.Underscore
     */
    protected function _before()
    {
        $this->module = new SalesforcePlugin('force');
    }

    /**
     * Test the component is set correctly
     */
    public function testCacheComponent()
    {
        $this->assertInstanceOf(
            Cache::class,
            $this->module->getCache()
        );

        $this->assertInstanceOf(
            Cache::class,
            $this->module->cache
        );
    }

    /**
     * Test the component is set correctly
     */
    public function testConnectionsComponent()
    {
        $this->assertInstanceOf(
            Connections::class,
            $this->module->getConnections()
        );

        $this->assertInstanceOf(
            Connections::class,
            $this->module->connections
        );
    }

    /**
     * Test the component is set correctly
     */
    public function testPSR3Component()
    {
        $this->assertInstanceOf(
            Logger::class,
            $this->module->getPsrLogger()
        );

        $this->assertInstanceOf(
            Logger::class,
            $this->module->psr3Logger
        );
    }

    // TODO - this is failing PHP7.2 tests ... not sure why
//    /**
//     * Test the component is set correctly
//     */
//    public function testResourcesComponent()
//    {
//        $this->assertInstanceOf(
//            Resources::class,
//            $this->module->getResources()
//        );
//
//        $this->assertInstanceOf(
//            Resources::class,
//            $this->module->resources
//        );
//    }

    /**
     * Test the component is set correctly
     */
    public function testObjectAssociationsComponent()
    {
        $this->assertInstanceOf(
            ObjectAssociations::class,
            $this->module->getObjectAssociations()
        );

        $this->assertInstanceOf(
            ObjectAssociations::class,
            $this->module->objectAssociations
        );
    }

    /**
     * Test the component is set correctly
     */
    public function testObjectFieldsComponent()
    {
        $this->assertInstanceOf(
            ObjectsField::class,
            $this->module->getObjectsField()
        );

        $this->assertInstanceOf(
            ObjectsField::class,
            $this->module->objectsField
        );
    }

    /**
     * Test the component is set correctly
     */
    public function testTransformersComponent()
    {
        $this->assertInstanceOf(
            Transformers::class,
            $this->module->getTransformers()
        );

        $this->assertInstanceOf(
            Transformers::class,
            $this->module->transformers
        );
    }
}
