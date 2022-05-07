<?php

namespace Exsio\PhpObjectDecorator\Tests\Fixtures;

class ChildObjectToDecorate extends ObjectToDecorate
{

    public string $publicProperty;

    protected string $protectedProperty;

    private string $privateProperty;

    /**
     * @param string $publicProperty
     */
    public function __construct()
    {
        parent::__construct();
        $this->publicProperty = "childPropertyValue";
        $this->protectedProperty = "childPropertyValue";
        $this->privateProperty = "childPropertyValue";
    }


    public function callInChild(): string
    {
        return "CHILD";
    }

    /**
     * @return string
     */
    public function getChildPublicProperty(): string
    {
        return $this->publicProperty;
    }

    /**
     * @return string
     */
    public function getChildProtectedProperty(): string
    {
        return $this->protectedProperty;
    }

    /**
     * @return string
     */
    public function getChildPrivateProperty(): string
    {
        return $this->privateProperty;
    }


}