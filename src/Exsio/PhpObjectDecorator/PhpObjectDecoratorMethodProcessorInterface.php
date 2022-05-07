<?php

namespace Exsio\PhpObjectDecorator;

interface PhpObjectDecoratorMethodProcessorInterface
{
    /**
     * @param PhpObjectDecoratorMethodDefinition $methodDefinition
     *
     * @return string
     */
    public function processMethod(PhpObjectDecoratorMethodDefinition $methodDefinition): string;
}