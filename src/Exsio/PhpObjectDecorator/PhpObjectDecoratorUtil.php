<?php

namespace Exsio\PhpObjectDecorator;

final class PhpObjectDecoratorUtil
{


    private function __construct()
    {
    }

    /**
     * @return array<string, PhpObjectDecoratorMethodDefinition>
     */
    public static function getMethodDefinitions(string $className): array
    {
        try {
            $result    = [];
            $reflector = new \ReflectionClass($className);
            foreach ($reflector->getMethods() as $method) {
                if (($method->isProtected() || $method->isPublic()) && !$method->isStatic() && !$method->isConstructor() && !$method->isDestructor()) {
                    $result[$method->getName()] = new PhpObjectDecoratorMethodDefinition($method);
                }
            }

            return $result;
        } catch (\Throwable $error) {
            if ($error instanceof PhpObjectDecoratorException) {
                throw $error;
            }
            throw new PhpObjectDecoratorException("Unable to reflectively scan the Class' Methods: ", $error);
        }
    }


}