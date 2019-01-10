<?php

namespace flipbox\craft\salesforce\tests;

use Codeception\Test\Unit;
use flipbox\craft\psr3\Logger;
use flipbox\craft\salesforce\Force as SalesforcePlugin;
use flipbox\craft\salesforce\services\Cache;

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
        $this->module = new SalesforcePlugin('salesforce');
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

//
//    /**
//     * Test the component is set correctly
//     */
//    public function testObjectAssociationsComponent()
//    {
//        $this->assertInstanceOf(
//            ObjectAssociations::class,
//            $this->module->getObjectAssociations()
//        );
//
//        $this->assertInstanceOf(
//            ObjectAssociations::class,
//            $this->module->objectAssociations
//        );
//    }

//    /**
//     * Test the component is set correctly
//     */
//    public function testObjectsFieldComponent()
//    {
//        $this->assertInstanceOf(
//            ObjectsField::class,
//            $this->module->getObjectsField()
//        );
//
//        $this->assertInstanceOf(
//            ObjectsField::class,
//            $this->module->objectsField
//        );
//    }

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

    // Todo - Travis php@7.2 fails
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
//
//    /**
//     * Test the component is set correctly
//     */
//    public function testTransformersComponent()
//    {
//        $this->assertInstanceOf(
//            Transformers::class,
//            $this->module->getTransformers()
//        );
//
//        $this->assertInstanceOf(
//            Transformers::class,
//            $this->module->transformers
//        );
//    }
}
