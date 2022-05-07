<?php

namespace Exsio\PhpObjectDecorator;

interface PhpDecoratedObjectInterface
{

    /**
     * @return string
     */
    public function getOriginalClass(): string;
}