<?php

namespace inisire\DataObject\Schema;

use inisire\DataObject\Schema\Type\Type;
use Symfony\Component\PropertyInfo\PropertyReadInfo;
use Symfony\Component\PropertyInfo\PropertyWriteInfo;


class Property
{
    public function __construct(
        private string $name,
        private Type $type,
        private bool $nullable = false,
        private mixed $default = null,
        private ?PropertyReadInfo $readInfo = null,
        private ?PropertyWriteInfo $writeInfo = null
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function isRequired(): bool
    {
        return !$this->isNullable() && !$this->isReadOnly();
    }

    public function isReadOnly(): bool
    {
        return $this->writeInfo === null;
    }

    public function getReadInfo(): ?PropertyReadInfo
    {
        return $this->readInfo;
    }

    public function getWriteInfo(): ?PropertyWriteInfo
    {
        return $this->writeInfo;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }
}