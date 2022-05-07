<?php

namespace Exsio\PhpObjectDecorator\Tests\Fixtures\Behaviors;

trait ValidBehavior1Trait
{
    public function callInValidBehavior1(): string
    {
        return 'VALID_BEHAVIOR_1';
    }

    function protectedContentMadePublic(): string
    {
        return $this->protectedContent();
    }
}