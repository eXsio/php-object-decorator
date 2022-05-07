<?php

namespace Exsio\PhpObjectDecorator;

interface PhpObjectDecoratorCacheInterface
{
    /**
     * @param string   $key
     * @param callable $provider
     *
     * @return string
     */
    public function get(string $key, callable $provider): string;

    /**
     * @return bool
     */
    public function isEnabled(): bool;
}