[![Build Status](https://app.travis-ci.com/eXsio/php-object-decorator.svg?branch=main)](https://app.travis-ci.com/eXsio/php-object-decorator)
[![Build Status](https://scrutinizer-ci.com/g/eXsio/php-object-decorator/badges/build.png?b=main)](https://scrutinizer-ci.com/g/eXsio/php-object-decorator/build-status/main)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/eXsio/php-object-decorator/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/eXsio/php-object-decorator/?branch=main)
[![Code Coverage](https://scrutinizer-ci.com/g/eXsio/php-object-decorator/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/eXsio/php-object-decorator/?branch=main)

# PHP Object Decorator

## A simple tool to dynamically add/modify behavior to/of your PHP Objects

### Motivation:

There are times when we have to bend the rules, especially when making slick and "magical" Developer Experience.
This tool can help you add any new Behaviors and modify any ```public ``` and ```protected``` methods of your PHP
Objects
during the runtime.

### My Use Case:

Why did I spawn this abomination? I needed exactly what is decribed in the prior paragraph. I've created a Symfony
Messenger
application that sends and receives Messages asynchronously. But I wanted it to be smart and cool. I wanted for the
Message Objects to have advanced features, like the ability to update the progress of the Message processing.
BUT, I only wanted the features to be available on the Handler/receiving side. The Messages on the Publisher/sending
side should be dump data holders. Only when the Messages arrive to the Handlers, they should have new and fancy
features.

Why go all this way, when I could've simply use some sort of injectable service to offer the exact same features? Well,
call me a sucker for encapsulation.
Even though this little tool bends all the OOP rules of PHP, the results (when done properly) are truly spectacular. We
get Objects with Behavior, and furthermore, this behavior is available just and only when you need it.
I always prefer the ```$object->doStuff()``` syntax over ```$service->doStuffTo($object)```. It's nicer. Its how thing
should be. So what if we need a little black magic trick to achieve this? :)

### Requirements:

The library has no external dependencies. It, however, requires PHP in version at least 8.1.2. Let's be modern. 
Let's not use outdated tech. 

### Usage:

Keeping things short, these are the things you can do to your Objects using this Tool:

```php

public function decorate() {
    /**
    * Define a Class name of the dynamically generated.
    */
    $className = "TestClass";
    
    /**
    * Point the Decorator to the Object Instance that you want to modify.
    */
    $result    = PhpObjectDecorator::decorate(new ChildObjectToDecorate(), $className)
        /**
        * Optionally, add the Namespace to the generated Class.
        */
        ->withNamespace("Exsio\PhpObjectDecorator\Tests")
        /**
        * Class Generation is expensive, you can cache the generated Class Definitions.
        * 
        * @see PhpObjectDecoratorCacheInterface
        */
        ->withCache(new TestCache())
        /**
        * You can add your Custom Behavior to your Object. Behavior is a pair of a PHP Interface, 
        * and a corresponding PHP Trait that implements all the methods from that Interface.
        */
        ->withBehavior(new PhpObjectDecoratorBehavior(ValidBehavior1Interface::class, ValidBehavior1Trait::class))
        /**
        * You can override and customize single, named PHP Method.
        */
        ->withMethodOverride(new PhpObjectDecoratorMethodOverride("callInParent",
            "
                return 'OVERRIDDEN METHOD ' . %CALL_PARENT%;
        "))
        /**
         * you can use a Method Processor to go over every public and protected Method, and modify them how you like.
         * Method Processor will skip Methods affected by Method Overrides.
         * 
         * @see PhpObjectDecoratorMethodProcessorInterface
         */
        ->withMethodProcessor(new TestMethodProcessor())
        /**
        * When you're ready, go ahead and build your Decorated Object.
        * 
        * @see PhpDecoratedObject
        */
        ->get();

    /**
    * Now you can get a string representation of your Decorated Class.
    * YOu can save it to a File for later usage. 
    * You can use PhpDecoratedObject::newInstanceOf() to instantiate it.
    * 
    * @see PhpDecoratedObject::newInstanceOf()
    */
    $body = $result->getBody();
    
    /**
    * Or you can immediately instantiate the Decorated Class and get a handle to the Instance,
    * that has your new Behaviors.
    */
    $instance  = $this->newInstance($result);
}


/**
* PHP 8.1 offers Intersection Types. 
* They prove that the Decorated Object indeed contains all the new Behaviors.
* Additionally, they allow for your IDE to properly assist you with intellisense and code completion.
*/
private function newInstance(PhpDecoratedObject $decoratedObject): ChildObjectToDecorate & PhpDecoratedObjectInterface & ValidBehavior1Interface
{
    return $decoratedObject->newInstance();
}

```

For executable examples, please feel free to browse the automated Unit Tests.
