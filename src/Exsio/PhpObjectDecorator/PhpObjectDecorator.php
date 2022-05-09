<?php

namespace Exsio\PhpObjectDecorator;

class PhpObjectDecorator
{

    private const METHOD_TEMPLATE = "
                %s
                {
                    %s
                }
            
        ";

    public const CALL_PARENT = "%CALL_PARENT%";

    private ?string $namespace = null;

    /** @var PhpObjectDecoratorBehavior[] */
    private array $behaviors = [];

    /** @var PhpObjectDecoratorMethodOverride[] */
    private array $methodOverrides = [];

    /** @var array<string, PhpObjectDecoratorMethodDefinition> */
    private array $methods = [];

    private ?PhpObjectDecoratorMethodProcessorInterface $methodProcessor = null;

    private bool $initialized = false;

    private ?PhpObjectDecoratorCacheInterface $cache = null;

    /**
     * @param mixed  $obj
     * @param string $className
     */
    private final function __construct(
        private readonly mixed $obj,
        private readonly string $className
    ) {
    }

    /**
     * @param mixed  $obj
     * @param string $className
     *
     * @return PhpObjectDecorator
     */
    public static function decorate(mixed $obj, string $className): PhpObjectDecorator
    {
        if (empty($className)) {
            throw new PhpObjectDecoratorException("Class Name of the decorated Object cannot be empty");
        }
        if (!is_object($obj)) {
            throw new PhpObjectDecoratorException(sprintf("You can only decorate PHP Objects, %s given", gettype($obj)));
        }
        if ($obj instanceof \stdClass) {
            throw new PhpObjectDecoratorException("StdClass decoration is not supported");
        }

        return new PhpObjectDecorator($obj, $className);
    }

    /**
     * @param string $namespace
     *
     * @return $this
     */
    public function withNamespace(string $namespace): PhpObjectDecorator
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @param PhpObjectDecoratorCacheInterface $cache
     *
     * @return $this
     */
    public function withCache(PhpObjectDecoratorCacheInterface $cache): PhpObjectDecorator
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @param PhpObjectDecoratorBehavior $behavior
     *
     * @return $this
     */
    public function withBehavior(PhpObjectDecoratorBehavior $behavior): PhpObjectDecorator
    {
        $this->behaviors[] = $behavior;

        return $this;
    }

    /**
     * @param PhpObjectDecoratorMethodOverride $methodOverride
     *
     * @return $this
     */
    public function withMethodOverride(PhpObjectDecoratorMethodOverride $methodOverride): PhpObjectDecorator
    {
        $this->methodOverrides[] = $methodOverride;

        return $this;
    }

    /**
     * @param PhpObjectDecoratorMethodProcessorInterface $methodProcessor
     *
     * @return $this
     */
    public function withMethodProcessor(PhpObjectDecoratorMethodProcessorInterface $methodProcessor): PhpObjectDecorator
    {
        $this->methodProcessor = $methodProcessor;

        return $this;
    }

    /**
     * @return PhpDecoratedObject
     */
    public function get(): PhpDecoratedObject
    {

        if ($this->cache && $this->cache->isEnabled()) {
            $body = $this->cache->get($this->className, function () {
                return $this->createBody();
            });
        } else {
            $body = $this->createBody();
        }

        return new PhpDecoratedObject($body, $this->obj, $this->getFullClassName());
    }

    /**
     * @return string
     */
    private function getFullClassName(): string
    {
        $namespace = $this->parseNamespace();

        return $namespace != '' ? sprintf("%s\\%s", $namespace, $this->className) : $this->className;
    }


    /**
     * @return string
     */
    private function createBody(): string
    {
        try {
            $this->initialize();
            $this->validate();

            list($interfaces, $traits) = $this->parseBehaviors();
            $methods           = $this->parseMethodOverrides() . $this->parseMethodProcessor();
            $namespace         = $this->parseNamespace();
            $className         = $this->className;
            $originalClassName = get_class($this->obj);

            $interfaces[] = PhpDecoratedObjectInterface::class;

            $interfacesStr = implode(", ", $interfaces);
            $traitsStr     = implode('\n', $traits);

            return "
            $namespace
        
            class $className extends $originalClassName implements $interfacesStr  
            {
                private string \$__originalClass = '$originalClassName';

                $traitsStr

                $methods
                
                public function getOriginalClass(): string
                {
                    return \$this->__originalClass;
                }
                
            }
        ";
        } catch (\Throwable $error) {
            if ($error instanceof PhpObjectDecoratorException) {
                throw $error;
            }
            throw new PhpObjectDecoratorException("Unable to create Decorated Object's Class Body: ", $error);
        }
    }

    /**
     * @return string
     */
    private function parseNamespace(): string
    {
        return $this->namespace ? sprintf("namespace %s;", $this->namespace) : "";
    }

    /**
     * @return void
     */
    private function initialize(): void
    {
        if ($this->initialized) {
            return;
        }
        $this->methods     = PhpObjectDecoratorUtil::getMethodDefinitions(get_class($this->obj));
        $this->initialized = true;
    }

    /**
     * @return void
     */
    private function validate(): void
    {
        $methodNames = array_keys($this->methods);
        foreach ($this->behaviors as $behavior) {
            $behavior->validate();
            $behaviorMethodNames = array_keys($behavior->getInterfaceMethods());
            foreach ($behaviorMethodNames as $behaviorMethodName) {
                if (in_array($behaviorMethodName, $methodNames)) {
                    throw new PhpObjectDecoratorException(sprintf("Duplicated Method: %s", $behaviorMethodName));
                }
            }
        }

        foreach ($this->methodOverrides as $methodOverride) {
            if (!in_array($methodOverride->getMethodName(), $methodNames)) {
                throw new PhpObjectDecoratorException(sprintf("Unknown Method cannot be overridden: %s", $methodOverride->getMethodName()));
            }
        }
    }

    /**
     * @return array[]
     */
    private function parseBehaviors(): array
    {
        $interfaces = [];
        $traits     = [];
        foreach ($this->behaviors as $behavior) {
            $interfaces[] = $behavior->getInterfaceClass();
            $traits[]     = sprintf('use %s;', $behavior->getTraitClass());
        }

        return [$interfaces, $traits];
    }

    /**
     * @return string
     */
    private function parseMethodOverrides(): string
    {
        $methods = '';
        foreach ($this->methodOverrides as $methodOverride) {
            $methodDefinition = $this->findMethodDefinition($methodOverride->getMethodName());

            $methods .= sprintf(self::METHOD_TEMPLATE,
                $methodDefinition->getHeader(), str_replace(self::CALL_PARENT, $methodDefinition->getParentCall(), $methodOverride->getMethodBody())
            );
        }

        return $methods;
    }

    /**
     * @return string
     */
    private function parseMethodProcessor(): string
    {
        $methods = '';
        if (!$this->methodProcessor) {
            return $methods;
        }
        $overriddenMethodNames = $this->getOverriddenMethodNames();
        foreach ($this->methods as $methodDefinition) {
            if (!in_array($methodDefinition->getName(), $overriddenMethodNames)) {
                $processedMethod = $this->methodProcessor->processMethod($methodDefinition);
                if (!empty($processedMethod)) {
                    $methods .= sprintf(self::METHOD_TEMPLATE, $methodDefinition->getHeader(),
                        str_replace(self::CALL_PARENT, $methodDefinition->getParentCall(), $processedMethod)
                    );
                }

            }
        }

        return $methods;
    }

    /**
     * @param string $methodName
     *
     * @return PhpObjectDecoratorMethodDefinition
     */
    private function findMethodDefinition(string $methodName): PhpObjectDecoratorMethodDefinition
    {
        if (array_key_exists($methodName, $this->methods)) {
            return $this->methods[$methodName];
        }
        throw new PhpObjectDecoratorException(sprintf("Unable to Find Method Definition: %s. This should've come up during the Validation stage.", $methodName));
    }

    /**
     * @return string[]
     */
    private function getOverriddenMethodNames(): array
    {
        $result = [];
        foreach ($this->methodOverrides as $methodOverride) {
            $result[] = $methodOverride->getMethodName();
        }

        return $result;
    }
}