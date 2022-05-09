<?php

namespace Exsio\PhpObjectDecorator;

class PhpObjectDecoratorMethodDefinition
{
    private readonly string $header;

    private readonly string $name;

    private readonly string $parentCall;

    /** @var string[] */
    private readonly array $parameterNames;

    /** @var array<string, string> */
    private readonly array $parametersWithTypes;

    /** @var array<string, string> */
    private readonly array $parametersWithDefaultValues;

    private readonly bool $returnsValue;

    private readonly \ReflectionMethod $reflector;

    /**
     * @param \ReflectionMethod $method
     *
     * @throws \ReflectionException
     */
    public function __construct(\ReflectionMethod $method)
    {
        $name                        = $method->getName();
        $params                      = [];
        $paramNames                  = [];
        $parametersWithTypes         = [];
        $parametersWithDefaultValues = [];
        /** @var \ReflectionParameter $parameter */
        foreach ($method->getParameters() as $parameter) {

            $methodParam                                = $parameter->getType() . ' $' . $parameter->getName();
            $parametersWithTypes[$parameter->getName()] = $parameter->getType();
            if ($parameter->isDefaultValueAvailable()) {
                if ($parameter->isDefaultValueConstant()) {
                    $methodParam                                        .= " = " . $parameter->getDefaultValueConstantName();
                    $parametersWithDefaultValues[$parameter->getName()] = $parameter->getDefaultValueConstantName();
                } else {
                    $defaultValue = is_array($parameter->getDefaultValue()) ? json_encode($parameter->getDefaultValue()) : $parameter->getDefaultValue();

                    $methodParam .= " = " . $defaultValue;

                    $parametersWithDefaultValues[$parameter->getName()] = $parameter->getDefaultValue();
                }

            }
            $params[]     = $methodParam;
            $paramNames[] = '$' . $parameter->getName();
        }
        $returnType                        = $method->getReturnType() ?: "";
        $modifier                          = $this->getModifier($method);
        $this->header                      = sprintf('%s function %s(%s)%s', $modifier, $name, implode(", ", $params), $returnType != "" ? ": " . $returnType : "");
        $this->parentCall                  = sprintf('parent::%s(%s)', $name, implode(", ", $paramNames));
        $this->name                        = $name;
        $this->parametersWithDefaultValues = $parametersWithDefaultValues;
        $this->parametersWithTypes         = $parametersWithTypes;
        $this->parameterNames              = $paramNames;
        $this->returnsValue                = $returnType != "" && $returnType != "never" && $returnType != "void";
        $this->reflector                   = $method;
    }

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @return string
     */
    public function getParentCall(): string
    {
        return $this->parentCall;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getParameterNames(): array
    {
        return $this->parameterNames;
    }

    /**
     * @return array<string, string>
     */
    public function getParametersWithTypes(): array
    {
        return $this->parametersWithTypes;
    }

    /**
     * @return array<string, string>
     */
    public function getParametersWithDefaultValues(): array
    {
        return $this->parametersWithDefaultValues;
    }

    /**
     * @return bool
     */
    public function returnsValue(): bool
    {
        return $this->returnsValue;
    }

    /**
     * @return \ReflectionMethod
     */
    public function getReflector(): \ReflectionMethod
    {
        return $this->reflector;
    }


    /**
     * @param \ReflectionMethod $method
     *
     * @return string
     */
    private function getModifier(\ReflectionMethod $method): string
    {
        switch (true) {
            case $method->isPublic():
                return 'public';
            case $method->isProtected():
                return 'protected';
            default:
                return '';
        }
    }
}