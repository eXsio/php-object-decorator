<?php

namespace Exsio\PhpObjectDecorator\Tests\Fixtures;

trait ObjectToDecorateTrait
{
    public function callInTrait(): string
    {
        return "TRAIT";
    }
}