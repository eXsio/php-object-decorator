<?php

namespace Exsio\PhpObjectDecorator\Tests\Fixtures\Behaviors;

interface InvalidBehaviorIncompatibleMethodInterface
{
    public function methodThatIsIncompatibleFromTheTrait(): string;
}