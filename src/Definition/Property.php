<?php


namespace inisire\DataObject\Definition;


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

    public function __construct(string $name, Definition $definition, bool $allowNull = false, bool $readOnly = false)
    {
        $this->name = $name;
        $this->definition = $definition;
        $this->allowNull = $allowNull;
        $this->readOnly = $readOnly;
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
}