<?php

namespace Exsio\PhpObjectDecorator\Tests;

use Exsio\PhpObjectDecorator\PhpDecoratedObject;
use Exsio\PhpObjectDecorator\PhpDecoratedObjectInterface;
use Exsio\PhpObjectDecorator\PhpObjectDecorator;
use Exsio\PhpObjectDecorator\PhpObjectDecoratorBehavior;
use Exsio\PhpObjectDecorator\PhpObjectDecoratorMethodOverride;
use Exsio\PhpObjectDecorator\Tests\Fixtures\Behaviors\ValidBehavior1Interface;
use Exsio\PhpObjectDecorator\Tests\Fixtures\Behaviors\ValidBehavior1Trait;
use Exsio\PhpObjectDecorator\Tests\Fixtures\ChildObjectToDecorate;
use Exsio\PhpObjectDecorator\Tests\Fixtures\ObjectToDecorate;
use Exsio\PhpObjectDecorator\Tests\Fixtures\TestMethodProcessor;
use PHPUnit\Framework\TestCase;

class PhpObjectDecorationInstantiationTest extends TestCase
{
    public function testShouldCreateAndInstantiateDecoratedObject_Full()
    {
        //when:
        $className = "TestClass_" . uniqid();
        $result    = PhpObjectDecorator::decorate(new ChildObjectToDecorate(), $className)
            ->withBehavior(new PhpObjectDecoratorBehavior(ValidBehavior1Interface::class, ValidBehavior1Trait::class))
            ->withMethodOverride(new PhpObjectDecoratorMethodOverride("callInParent",
                "
                    return 'OVERRIDDEN METHOD ' . %CALL_PARENT%;
            "))
            ->withMethodProcessor(new TestMethodProcessor())
            ->get();
        $instance  = $this->newInstance($result);
        //then:
        $this->assertInstanceOf(ChildObjectToDecorate::class, $instance);
        $this->assertInstanceOf(ObjectToDecorate::class, $instance);
        $this->assertInstanceOf(PhpDecoratedObjectInterface::class, $instance);
        $this->assertInstanceOf(ValidBehavior1Interface::class, $instance);

        $this->assertEquals("VALID_BEHAVIOR_1", $instance->callInValidBehavior1());
        $this->assertEquals("OVERRIDDEN METHOD PARENT", $instance->callInParent());
        $this->assertEquals("childPropertyValue", $instance->getPublicProperty());
        $this->assertEquals("childPropertyValue", $instance->getProtectedProperty());
        $this->assertEquals("propertyValue", $instance->getPrivateProperty());
        $this->assertEquals("childPropertyValue", $instance->getChildPublicProperty());
        $this->assertEquals("childPropertyValue", $instance->getChildProtectedProperty());
        $this->assertEquals("childPropertyValue", $instance->getChildPrivateProperty());
        $this->assertEquals("TRAIT", $instance->callInTrait());
        $this->assertEquals("PROTECTED_CONTENT", $instance->protectedContentMadePublic());
    }

    public function testShouldCreateAndInstantiateDecoratedObject_NoMethodChanges()
    {
        //when:
        $className = "TestClass_" . uniqid();
        $result    = PhpObjectDecorator::decorate(new ChildObjectToDecorate(), $className)
            ->withBehavior(new PhpObjectDecoratorBehavior(ValidBehavior1Interface::class, ValidBehavior1Trait::class))
            ->get();
        $instance  = $this->newInstance($result);
        //then:
        $this->assertInstanceOf(ChildObjectToDecorate::class, $instance);
        $this->assertInstanceOf(ObjectToDecorate::class, $instance);
        $this->assertInstanceOf(PhpDecoratedObjectInterface::class, $instance);
        $this->assertInstanceOf(ValidBehavior1Interface::class, $instance);

        $this->assertEquals("VALID_BEHAVIOR_1", $instance->callInValidBehavior1());
        $this->assertEquals("PARENT", $instance->callInParent());
        $this->assertEquals("childPropertyValue", $instance->getPublicProperty());
        $this->assertEquals("childPropertyValue", $instance->getProtectedProperty());
        $this->assertEquals("propertyValue", $instance->getPrivateProperty());
        $this->assertEquals("childPropertyValue", $instance->getChildPublicProperty());
        $this->assertEquals("childPropertyValue", $instance->getChildProtectedProperty());
        $this->assertEquals("childPropertyValue", $instance->getChildPrivateProperty());
        $this->assertEquals("TRAIT", $instance->callInTrait());
        $this->assertEquals("PROTECTED_CONTENT", $instance->protectedContentMadePublic());
    }

    public function testShouldCreateAndInstantiateDecoratedObject_NoBehavior()
    {
        //when:
        $className = "TestClass_" . uniqid();
        $result    = PhpObjectDecorator::decorate(new ChildObjectToDecorate(), $className)
            ->withMethodOverride(new PhpObjectDecoratorMethodOverride("callInParent",
                "
                    return 'OVERRIDDEN METHOD ' . %CALL_PARENT%;
            "))
            ->withMethodProcessor(new TestMethodProcessor())
            ->get();
        $instance  = $result->newInstance();
        //then:
        $this->assertInstanceOf(ChildObjectToDecorate::class, $instance);
        $this->assertInstanceOf(ObjectToDecorate::class, $instance);
        $this->assertInstanceOf(PhpDecoratedObjectInterface::class, $instance);

        $this->assertEquals("OVERRIDDEN METHOD PARENT", $instance->callInParent());
        $this->assertEquals("childPropertyValue", $instance->getPublicProperty());
        $this->assertEquals("childPropertyValue", $instance->getProtectedProperty());
        $this->assertEquals("propertyValue", $instance->getPrivateProperty());
        $this->assertEquals("childPropertyValue", $instance->getChildPublicProperty());
        $this->assertEquals("childPropertyValue", $instance->getChildProtectedProperty());
        $this->assertEquals("childPropertyValue", $instance->getChildPrivateProperty());
        $this->assertEquals("TRAIT", $instance->callInTrait());
    }

    private function newInstance(PhpDecoratedObject $decoratedObject): ChildObjectToDecorate & PhpDecoratedObjectInterface & ValidBehavior1Interface
    {
        return $decoratedObject->newInstance();
    }
}