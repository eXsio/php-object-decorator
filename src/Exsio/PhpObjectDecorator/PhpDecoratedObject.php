<?php

namespace Exsio\PhpObjectDecorator;

class PhpDecoratedObject
{

    public function __construct(
        private readonly string $body,
        private readonly object $obj,
        private readonly string $className,

    ) {
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return object
     */
    public function newInstance(): object
    {
        return self::newInstanceOf($this->className, $this->body, $this->obj);
    }

    /**
     * @return object
     */
    public static function newInstanceOf(string $className, string $body, object $obj): object
    {
        try {
            if (!class_exists($className)) {
                eval($body);
            }
            $reflector = new \ReflectionClass($className);
            $instance  = $reflector->newInstanceWithoutConstructor();
            self::cloneProperties($instance, get_class($obj), $obj);

            return $instance;
        } catch (\Throwable $error) {
            if ($error instanceof PhpObjectDecoratorException) {
                throw $error;
            }
            throw new PhpObjectDecoratorException("Unable to Instantiate Decorated Class: ", $error);
        }
    }

    /**
     * @param mixed  $instance
     * @param string $class
     *
     * @return void
     * @throws \ReflectionException
     */
    private static function cloneProperties(mixed $instance, string $class, object $obj): void
    {
        $reflector  = new \ReflectionClass($class);
        $properties = $reflector->getProperties();
        foreach ($properties as $property) {
            //sometimes \ReflectionClass returns properties from the subclasses, and that can cause
            //an error, when trying to set a readonly property value from invalid scope
            if ($property->isInitialized($obj) && $property->getDeclaringClass()->getName() === $class) {
                $originalValue = $property->getValue($obj);
                $property->setValue($instance, $originalValue);
            }
        }
        foreach ($reflector->getTraits() as $traitClass) {
            self::cloneProperties($instance, $traitClass->getName(), $obj);
        }
        if ($reflector->getParentClass()) {
            self::cloneProperties($instance, $reflector->getParentClass()->getName(), $obj);
        }
    }


}