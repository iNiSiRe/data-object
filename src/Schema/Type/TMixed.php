<?php


namespace inisire\DataObject\Schema\Type;


class TMixed extends TPrimitive
{
    private array $types;

    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * @return array<Type>
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}