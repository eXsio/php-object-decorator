<?php

namespace Exsio\PhpObjectDecorator;

class PhpObjectDecoratorException extends \RuntimeException
{

    /**
     * @param string          $msg
     * @param \Throwable|null $prev
     */
    public function __construct(string $msg, ?\Throwable $prev = null)
    {
        parent::__construct($msg, -1, $prev);
    }
}