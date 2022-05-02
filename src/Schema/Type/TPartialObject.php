<?php


namespace inisire\DataObject\Schema\Type;


class TPartialObject implements Type
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
}