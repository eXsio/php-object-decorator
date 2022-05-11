[![Build Status](https://app.travis-ci.com/eXsio/php-object-decorator.svg?branch=main)](https://app.travis-ci.com/eXsio/php-object-decorator)
[![Build Status](https://scrutinizer-ci.com/g/eXsio/php-object-decorator/badges/build.png?b=main)](https://scrutinizer-ci.com/g/eXsio/php-object-decorator/build-status/main)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/eXsio/php-object-decorator/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/eXsio/php-object-decorator/?branch=main)
[![Code Coverage](https://scrutinizer-ci.com/g/eXsio/php-object-decorator/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/eXsio/php-object-decorator/?branch=main)


# PHP Object Decorator

## A simple tool to dynamically add/modify behavior to/of your PHP Objects

### Motivation:

There are times when we have to bend the rules, especially when making a slick and "magical" Developer Experience.
This tool can help you to dynamically add any new Behaviors and modify any ```public ``` and ```protected``` methods of your PHP
Objects in the runtime. With this Tool you can have a dumb data-holder Object and give it superpowers right when and where it needs it to kick some butts.

**WARNING:**
Meta-programming is not for the faint-hearted. You should really explore your options before jumping into that particular rabbit hole.

You were warned.

### Requirements:

The library has no external dependencies. It, however, requires PHP in version at least 8.1.2. Let's be modern. 
Let's not use outdated tech. Also, PHP 8.1 has Intersection Types that pair really nicely with the theme of this library.

### Installation:

```composer require exsio/php-object-decorator```

### Usage:

Keeping things short, these are the things you can do to your Objects using this Tool:

```php
/**
* PHP 8.1 offers Intersection Types. 
* They prove that the Decorated Object indeed contains all the new Behaviors.
* Additionally, they allow for your IDE to properly assist you with intellisense and code completion.
* PhpDecoratedObjectInterface is always added as an additional, default Behavior to every decorated Object Instance.
*/
public function decorate(ObjectToDecorate $obj): ObjectToDecorate & PhpDecoratedObjectInterface & BehaviorInterface
{
    /**
    * Point the Decorator to the Object Instance that you want to modify.
    */
    $decorated = PhpObjectDecorator::decorate($obj)
        /**
        * Optionally, you can define a custom Class Name for the Decorated Object;
        * Otherwise it will be an original Fully Qualified Class Name with \ replaced with _ and a _PhpObjectDecorated suffix.
        */
        ->withClassName("DecoratedObject")
        /**
        * Optionally, add the Namespace to the generated Class.
        * Otherwise, the Decorated Object's Class will have no Namespace.
        */
        ->withNamespace("Exsio\PhpObjectDecorator\Examples")
        /**
        * Class Generation is expensive, you can cache the generated Class Definitions.
        * 
        * @see PhpObjectDecoratorCacheInterface
        */
        ->withCache(new DecorationCache())
        /**
        * You can add your Custom Behavior to your Object. Behavior is a pair of a PHP Interface, 
        * and a corresponding PHP Trait that implements all the methods from that Interface.
        *
        * The Tool validates whether the Trait implments all the Interface's methods and if the method declarations match.
        */
        ->withBehavior(new PhpObjectDecoratorBehavior(BehaviorInterface::class, BehaviorTrait::class))
        /**
        * You can override and customize single, named PHP Method.
        * The %CALL_PARENT% placeholder will be replaced automatically with the parent:: call to the original Method.
        *
        * The Tool validates whether the Method you want to override exists in the original Class.
        * You can't override Methods from your Behaviors. Only from the original Class (and it's parents).
        */
        ->withMethodOverride(new PhpObjectDecoratorMethodOverride("methodNameToOverride",
            "
                return 'OVERRIDDEN METHOD ' . %CALL_PARENT%;
        "))
        /**
         * You can use a Method Processor to go over every public and protected Method, and modify them how you like.
         * Method Processor will skip Methods affected by Method Overrides.
         *
         * You can skip the Method by returing self::SKIP from the Processor.
         * 
         * @see PhpObjectDecoratorMethodProcessorInterface
         */
        ->withMethodProcessor(new DecorationMethodProcessor())
        /**
        * When you're ready, go ahead and build your Decorated Object.
        * 
        * @see PhpDecoratedObject
        */
        ->get();

    /**
    * Now you can get a string representation of your Decorated Class.
    * YOu can save it to a File for later usage. 
    * You can use PhpDecoratedObject::newInstanceOf() to instantiate it, 
    * or you can simply load it using an autoloader and call its Constructor.
    * 
    * @see PhpDecoratedObject::newInstanceOf()
    */
    $body = $decorated->getBody();
    
    /**
    * Or you can immediately instantiate the Decorated Class and get a handle to the Instance,
    * that has your new Behaviors.
    */
    return $decorated->newInstance();
}

```

For executable examples, please feel free to browse the automated Unit Tests.
