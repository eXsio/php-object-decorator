<?php

namespace Exsio\PhpObjectDecorator;

class PhpObjectDecoratorBehavior
{

    /** @var array<string, PhpObjectDecoratorMethodDefinition> */
    private array $interfaceMethods = [];

    /** @var array<string, PhpObjectDecoratorMethodDefinition> */
    private array $traitMethods = [];

    private bool $initialized = false;

    /**
     * @param string $interfaceClass
     * @param string $traitClass
     */
    public function __construct(
        private readonly string $interfaceClass,
        private readonly string $traitClass
    ) {
    }


    /**
     * @return string
     */
    public function getInterfaceClass(): string
    {
        return $this->interfaceClass;
    }

    /**
     * @return string
     */
    public function getTraitClass(): string
    {
        return $this->traitClass;
    }

    /**
     * @return array<string, PhpObjectDecoratorMethodDefinition>
     */
    public function getInterfaceMethods(): array
    {
        $this->initialize();

        return $this->interfaceMethods;
    }

    /**
     * @return array<string, PhpObjectDecoratorMethodDefinition>
     */
    public function getTraitMethods(): array
    {
        $this->initialize();

        return $this->traitMethods;
    }

    /**
     * @return void
     */
    public function validate(): void
    {
        $this->initialize();
        $reflector = new \ReflectionClass($this->interfaceClass);
        if(!$reflector->isInterface()) {
            throw new PhpObjectDecoratorException(sprintf("First argument of the Behavior has to be an Interface, %s given", $this->interfaceClass));
        }
        $reflector = new \ReflectionClass($this->traitClass);
        if(!$reflector->isTrait()) {
            throw new PhpObjectDecoratorException(sprintf("Second argument of the Behavior has to be a Trait, %s given", $this->interfaceClass));
        }
        foreach ($this->interfaceMethods as $name => $definition) {
            if (!array_key_exists($name, $this->traitMethods)) {
                throw new PhpObjectDecoratorException(sprintf("Behavior Trait %s doesn't implement the corresponding Interface Method: %s::%s()", $this->traitClass, $this->interfaceMethods, $name));
            }
            $interfaceDefinition = $definition->getHeader();
            $traitDefinition     = $this->traitMethods[$name]->getHeader();

            if ($interfaceDefinition != $traitDefinition) {
                throw new PhpObjectDecoratorException(sprintf("Behavior Trait's Method %s::%s is not compatible with the corresponding Interface Method: %s::%s",
                        $this->traitClass, $traitDefinition, $this->interfaceMethods, $interfaceDefinition)
                );
            }
        }
    }

    /**
     * @return void
     */
    private function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->interfaceMethods = PhpObjectDecoratorUtil::getMethodDefinitions($this->interfaceClass);
        $this->traitMethods     = PhpObjectDecoratorUtil::getMethodDefinitions($this->traitClass);
        $this->initialized      = true;
    }


}