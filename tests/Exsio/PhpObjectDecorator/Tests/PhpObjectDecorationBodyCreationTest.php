<?php

namespace Exsio\PhpObjectDecorator\Tests;

use Exsio\PhpObjectDecorator\PhpObjectDecorator;
use Exsio\PhpObjectDecorator\PhpObjectDecoratorBehavior;
use Exsio\PhpObjectDecorator\PhpObjectDecoratorMethodOverride;
use Exsio\PhpObjectDecorator\Tests\Fixtures\Behaviors\ValidBehavior1Interface;
use Exsio\PhpObjectDecorator\Tests\Fixtures\Behaviors\ValidBehavior1Trait;
use Exsio\PhpObjectDecorator\Tests\Fixtures\ChildObjectToDecorate;
use Exsio\PhpObjectDecorator\Tests\Fixtures\TestMethodProcessor;
use PHPUnit\Framework\TestCase;

class PhpObjectDecorationBodyCreationTest extends TestCase
{
    const EXPECTED = '
            namespace Exsio\\PhpObjectDecorator\\Tests;
        
            class Exsio_PhpObjectDecorator_Tests_Fixtures_ChildObjectToDecorate_PhpDecoratedObject extends Exsio\\PhpObjectDecorator\\Tests\\Fixtures\\ChildObjectToDecorate implements Exsio\\PhpObjectDecorator\\Tests\\Fixtures\\Behaviors\\ValidBehavior1Interface, Exsio\\PhpObjectDecorator\\PhpDecoratedObjectInterface  
            {
                private string $__originalClass = \'Exsio\\PhpObjectDecorator\\Tests\\Fixtures\\ChildObjectToDecorate\';

                use Exsio\\PhpObjectDecorator\\Tests\\Fixtures\\Behaviors\\ValidBehavior1Trait;

                
                public function callInParent(): string
                {
                    
                    return \'OVERRIDDEN METHOD \' . parent::callInParent();
            
                }
            
        
                public function callInChild(): string
                {
                    
                    echo \'PROCESSED METHOD\';
                    return parent::callInChild();
        
                }
            
        
                public function getChildPublicProperty(): string
                {
                    
                    echo \'PROCESSED METHOD\';
                    return parent::getChildPublicProperty();
        
                }
            
        
                public function getChildProtectedProperty(): string
                {
                    
                    echo \'PROCESSED METHOD\';
                    return parent::getChildProtectedProperty();
        
                }
            
        
                public function getChildPrivateProperty(): string
                {
                    
                    echo \'PROCESSED METHOD\';
                    return parent::getChildPrivateProperty();
        
                }
            
        
                public function getPrivateProperty(): string
                {
                    
                    echo \'PROCESSED METHOD\';
                    return parent::getPrivateProperty();
        
                }
            
        
                public function getPublicProperty(): string
                {
                    
                    echo \'PROCESSED METHOD\';
                    return parent::getPublicProperty();
        
                }
            
        
                public function getProtectedProperty(): string
                {
                    
                    echo \'PROCESSED METHOD\';
                    return parent::getProtectedProperty();
        
                }
            
        
                protected function protectedContent(): string
                {
                    
                    echo \'PROCESSED METHOD\';
                    return parent::protectedContent();
        
                }
            
        
                public function callInTrait(): string
                {
                    
                    echo \'PROCESSED METHOD\';
                    return parent::callInTrait();
        
                }
            
        
                
                public function getOriginalClass(): string
                {
                    return $this->__originalClass;
                }
                
            }
        ';


    public function testShouldCreateBodyOfDecoratedObject()
    {
        //when:
        $result    = PhpObjectDecorator::decorate(new ChildObjectToDecorate())
            ->withNamespace("Exsio\PhpObjectDecorator\Tests")
            ->withBehavior(new PhpObjectDecoratorBehavior(ValidBehavior1Interface::class, ValidBehavior1Trait::class))
            ->withMethodOverride(new PhpObjectDecoratorMethodOverride("callInParent",
                "
                    return 'OVERRIDDEN METHOD ' . %CALL_PARENT%;
            "))
            ->withMethodProcessor(new TestMethodProcessor())
            ->get();

        $body = $result->getBody();

        //then:
        $this->assertEquals(trim(self::EXPECTED), trim($body));
    }


}