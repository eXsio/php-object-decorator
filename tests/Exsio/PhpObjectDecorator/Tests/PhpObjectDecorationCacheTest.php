<?php

namespace Exsio\PhpObjectDecorator\Tests;

use Exsio\PhpObjectDecorator\PhpObjectDecorator;
use Exsio\PhpObjectDecorator\Tests\Fixtures\ChildObjectToDecorate;
use Exsio\PhpObjectDecorator\Tests\Fixtures\TestCache;
use PHPUnit\Framework\TestCase;

class PhpObjectDecorationCacheTest extends TestCase
{

    public function testShouldCacheTheDecoratedObjectClassIfCacheIsEnabled()
    {
        //when:
        $className = "TestClass_" . uniqid();
        $cache     = new TestCache();
        $cache->setEnabled(true);
        $decorator = PhpObjectDecorator::decorate(new ChildObjectToDecorate())
            ->withClassName($className)
            ->withCache($cache);
        $result    = $decorator->get();
        $result    = $decorator->get();

        //then:
        $this->assertTrue($cache->contains($className));
        $this->assertEquals($cache->get($className, function () {
        }), $result->getBody());

    }

    public function testShouldNotCacheTheDecoratedObjectClassIfCacheIsDisabled()
    {
        //when:
        $className = "TestClass_" . uniqid();
        $cache     = new TestCache();
        $cache->setEnabled(false);
        $decorator = PhpObjectDecorator::decorate(new ChildObjectToDecorate())
            ->withClassName($className)
            ->withCache($cache);
        $result    = $decorator->get();
        $result    = $decorator->get();

        //then:
        $this->assertFalse($cache->contains($className));


    }
}