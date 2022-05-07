<?php

namespace Exsio\PhpObjectDecorator\Tests;

use Exsio\PhpObjectDecorator\PhpObjectDecorator;
use Exsio\PhpObjectDecorator\PhpObjectDecoratorBehavior;
use Exsio\PhpObjectDecorator\PhpObjectDecoratorException;
use Exsio\PhpObjectDecorator\PhpObjectDecoratorMethodOverride;
use Exsio\PhpObjectDecorator\Tests\Fixtures\Behaviors\InvalidBehaviorDuplicatedMethodInterface;
use Exsio\PhpObjectDecorator\Tests\Fixtures\Behaviors\InvalidBehaviorDuplicatedMethodTrait;
use Exsio\PhpObjectDecorator\Tests\Fixtures\Behaviors\InvalidBehaviorIncompatibleMethodInterface;
use Exsio\PhpObjectDecorator\Tests\Fixtures\Behaviors\InvalidBehaviorIncompatibleMethodTrait;
use Exsio\PhpObjectDecorator\Tests\Fixtures\Behaviors\InvalidBehaviorMissingMethodInterface;
use Exsio\PhpObjectDecorator\Tests\Fixtures\Behaviors\InvalidBehaviorMissingMethodTrait;
use Exsio\PhpObjectDecorator\Tests\Fixtures\ChildObjectToDecorate;
use PHPUnit\Framework\TestCase;

class PhpObjectDecorationValidationTest extends TestCase
{

    public function testShouldDetectInvalidBehavior_MissingMethods()
    {
        //expect:
        $this->expectException(PhpObjectDecoratorException::class);

        //when:
        $className = "TestClass_" . uniqid();
        PhpObjectDecorator::decorate(new ChildObjectToDecorate(), $className)
            ->withBehavior(new PhpObjectDecoratorBehavior(InvalidBehaviorMissingMethodInterface::class, InvalidBehaviorMissingMethodTrait::class))
            ->get();

    }

    public function testShouldDetectInvalidBehavior_InvalidBehaviorArguments_TraitInsteadOfInterface()
    {
        //expect:
        $this->expectException(PhpObjectDecoratorException::class);

        //when:
        $className = "TestClass_" . uniqid();
        PhpObjectDecorator::decorate(new ChildObjectToDecorate(), $className)
            ->withBehavior(new PhpObjectDecoratorBehavior(InvalidBehaviorIncompatibleMethodTrait::class, InvalidBehaviorMissingMethodTrait::class))
            ->get();

    }

    public function testShouldDetectInvalidBehavior_InvalidBehaviorArguments_InterfaceInsteadOfTrait()
    {
        //expect:
        $this->expectException(PhpObjectDecoratorException::class);

        //when:
        $className = "TestClass_" . uniqid();
        PhpObjectDecorator::decorate(new ChildObjectToDecorate(), $className)
            ->withBehavior(new PhpObjectDecoratorBehavior(InvalidBehaviorMissingMethodInterface::class, InvalidBehaviorMissingMethodInterface::class))
            ->get();

    }

    public function testShouldDetectInvalidBehavior_IncompatibleMethods()
    {
        //expect:
        $this->expectException(PhpObjectDecoratorException::class);

        //when:
        $className = "TestClass_" . uniqid();
        PhpObjectDecorator::decorate(new ChildObjectToDecorate(), $className)
            ->withBehavior(new PhpObjectDecoratorBehavior(InvalidBehaviorIncompatibleMethodInterface::class, InvalidBehaviorIncompatibleMethodTrait::class))
            ->get();

    }

    public function testShouldDetectInvalidBehavior_DuplicatedMethods()
    {
        //expect:
        $this->expectException(PhpObjectDecoratorException::class);

        //when:
        $className = "TestClass_" . uniqid();
        PhpObjectDecorator::decorate(new ChildObjectToDecorate(), $className)
            ->withBehavior(new PhpObjectDecoratorBehavior(InvalidBehaviorDuplicatedMethodInterface::class, InvalidBehaviorDuplicatedMethodTrait::class))
            ->get();

    }

    public function testShouldDetectInvalidMethodOverride()
    {
        //expect:
        $this->expectException(PhpObjectDecoratorException::class);

        //when:
        $className = "TestClass_" . uniqid();
        PhpObjectDecorator::decorate(new ChildObjectToDecorate(), $className)
            ->withMethodOverride(new PhpObjectDecoratorMethodOverride("callInParenta",
                "
                    return 'OVERRIDDEN METHOD ' . %CALL_PARENT%;
            "))
            ->get();

    }
}