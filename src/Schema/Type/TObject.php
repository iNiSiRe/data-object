<?php


namespace inisire\DataObject\Schema\Type;


use inisire\DataObject\Serializer\ObjectSerializer;

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

    public function getSerializer(): string
    {
        return ObjectSerializer::class;
    }
}