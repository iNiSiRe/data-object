<?php

namespace inisire\DataObject\Definition;

use Symfony\Component\PropertyInfo\PropertyReadInfo;
use Symfony\Component\PropertyInfo\PropertyWriteInfo;

class Property
{
    public string $name;

    public array $errors = [];

    /**
     * @var Definition
     */
    public Definition $definition;

    private bool $allowNull;

    private bool $readOnly;

    /**
     * @var array<string, PropertyReadInfo|PropertyWriteInfo>
     */
    private array $accessors = [];
    
    public function __construct(string $name, Definition $definition, bool $allowNull = false, bool $readOnly = false, array $accessors = [])
    {
        $this->name = $name;
        $this->definition = $definition;
        $this->allowNull = $allowNull;
        $this->readOnly = $readOnly;
        $this->accessors = $accessors;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Definition
     */
    public function getType(): Definition
    {
        return $this->definition;
    }

    /**
     * @return bool
     */
    public function isAllowNull(): bool
    {
        return $this->allowNull;
    }

    public function isRequired(): bool
    {
        return !$this->isAllowNull() && !$this->isReadOnly();
    }

    /**
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }
    
    public function getReadInfo(): ?PropertyReadInfo
    {
        return $this->accessors['read'] ?? null;
    }
}