<?php

namespace Exsio\PhpObjectDecorator\Tests\Fixtures;

class ObjectToDecorate
{
    public string $publicProperty;

    protected string $protectedProperty;

    private string $privateProperty;


    public function __construct()
    {
        $this->publicProperty = "propertyValue";
        $this->protectedProperty = "propertyValue";
        $this->privateProperty = "propertyValue";
    }

    use ObjectToDecorateTrait;


    public function callInParent(): string
    {
        return "PARENT";
    }

    /**
     * @return string
     */
    public function getPrivateProperty(): string
    {
        return $this->privateProperty;
    }

    /**
     * @return string
     */
    public function getPublicProperty(): string
    {
        return $this->publicProperty;
    }

    /**
     * @return string
     */
    public function getProtectedProperty(): string
    {
        return $this->protectedProperty;
    }

    protected function protectedContent(): string
    {
        return "PROTECTED_CONTENT";
    }


}