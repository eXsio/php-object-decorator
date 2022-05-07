<?php

namespace Exsio\PhpObjectDecorator;

class PhpObjectDecoratorMethodOverride
{

    /**
     * @param string $methodName
     * @param string $methodBody
     */
    public function __construct(
        private readonly string $methodName,
        private readonly string $methodBody
    )
    {
    }

    /**
     * @return string
     */
    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * @return string
     */
    public function getMethodBody(): string
    {
        return $this->methodBody;
    }


}