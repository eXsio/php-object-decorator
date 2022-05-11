<?php

namespace Exsio\PhpObjectDecorator\Tests\Fixtures\Behaviors;

trait ValidBehavior2Trait
{
    public function callInValidBehavior2(): string
    {
        return 'VALID_BEHAVIOR_2';
    }
}