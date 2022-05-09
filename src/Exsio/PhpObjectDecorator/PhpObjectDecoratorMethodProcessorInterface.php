<?php

namespace Exsio\PhpObjectDecorator;

interface PhpObjectDecoratorMethodProcessorInterface
{

    const SKIP = "";

    /**
     * @param PhpObjectDecoratorMethodDefinition $methodDefinition
     *
     * @return string
     */
    public function processMethod(PhpObjectDecoratorMethodDefinition $methodDefinition): string;
}