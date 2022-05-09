<?php

namespace Exsio\PhpObjectDecorator\Tests\Fixtures;

use Exsio\PhpObjectDecorator\PhpObjectDecoratorMethodDefinition;
use Exsio\PhpObjectDecorator\PhpObjectDecoratorMethodProcessorInterface;

class TestMethodProcessor implements PhpObjectDecoratorMethodProcessorInterface
{

    function processMethod(PhpObjectDecoratorMethodDefinition $methodDefinition): string
    {
        if ($methodDefinition->getReflector()->isConstructor()) {
            return self::SKIP;
        }
        $parentCall = $methodDefinition->returnsValue() ? "return " . $methodDefinition->getParentCall() . ";" : $methodDefinition->getParentCall() . ";";

        return "
                    echo 'PROCESSED METHOD';
                    $parentCall
        ";
    }
}