<?php

namespace Exsio\PhpObjectDecorator\Tests\Fixtures\Behaviors;

interface ValidBehavior1Interface
{
    function callInValidBehavior1(): string;

    function protectedContentMadePublic(): string;
}