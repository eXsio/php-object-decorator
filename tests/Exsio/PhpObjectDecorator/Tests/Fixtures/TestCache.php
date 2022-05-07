<?php

namespace Exsio\PhpObjectDecorator\Tests\Fixtures;

use Exsio\PhpObjectDecorator\PhpObjectDecoratorCacheInterface;

class TestCache implements PhpObjectDecoratorCacheInterface
{

    private array $cache = [];

    private bool $enabled = true;

    public function get(string $key, callable $provider): string
    {
        if (!$this->contains($key)) {
            $value             = $provider();
            $this->cache[$key] = $value;

            return $value;
        }

        return $this->cache[$key];
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function contains(string $key)
    {
        return array_key_exists($key, $this->cache);
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

}