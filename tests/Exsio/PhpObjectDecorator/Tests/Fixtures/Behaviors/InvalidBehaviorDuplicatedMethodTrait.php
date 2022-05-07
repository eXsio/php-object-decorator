<?php

namespace Exsio\PhpObjectDecorator\Tests\Fixtures\Behaviors;

trait InvalidBehaviorDuplicatedMethodTrait
{
    public function callInChild(): string
    {

    }
}