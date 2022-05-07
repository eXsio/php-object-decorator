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
        $prevMsg = $prev ? ' ' . $prev->getMessage() : '';
        parent::__construct($msg . $prevMsg, -1, $prev);
    }
}