<?php


namespace inisire\DataObject\Definition;


class TMixed implements Definition
{
    private array $definitions;

    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * @return array<Definition>
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }
}