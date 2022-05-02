<?php


namespace inisire\DataObject\Schema\Type;


class TObject implements Type
{
    private string $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function createInstance(): object
    {
        return new $this->class();
    }
}