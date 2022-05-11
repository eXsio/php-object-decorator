<?php

namespace Exsio\PhpObjectDecorator\Tests;

use Exsio\PhpObjectDecorator\PhpObjectDecorator;
use Exsio\PhpObjectDecorator\PhpObjectDecoratorException;
use Exsio\PhpObjectDecorator\Tests\Fixtures\ChildObjectToDecorate;
use PHPUnit\Framework\TestCase;

class InitializationTest extends TestCase
{

    public function testShouldInitializeWithObjectAndClassName()
    {
        //when:
        PhpObjectDecorator::decorate(new ChildObjectToDecorate());

        //then:
        $this->assertTrue(true);
    }

    /**
     * @dataProvider invalidClassNameProvider
     */
    public function testShouldNotInitializeWithNonObjectAndClassName(mixed $obj)
    {
        //expect:
        $this->expectException(PhpObjectDecoratorException::class);

        //when:
        PhpObjectDecorator::decorate($obj);
    }

    /**
     * @dataProvider invalidClassNameProvider
     */
    public function testShouldNotInitializeWithInvalidClassName(mixed $className)
    {
        //expect:
        $this->expectException(PhpObjectDecoratorException::class);

        //when:
        PhpObjectDecorator::decorate(new ChildObjectToDecorate())->withClassName($className);
    }

    public function invalidObjectProvider(): array
    {
        return [["string"], [[]], [10], [true], [null], [new \stdClass()]];
    }

    public function invalidClassNameProvider(): array
    {
        return [[""]];
    }


}