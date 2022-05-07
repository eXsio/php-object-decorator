<?php

namespace Exsio\PhpObjectDecorator\Tests\Fixtures\Behaviors;

trait InvalidBehaviorIncompatibleMethodTrait
{
    public function methodThatIsIncompatibleFromTheTrait(): int
    {

    }
}