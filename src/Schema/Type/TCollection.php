<?php


namespace inisire\DataObject\Schema\Type;


class TCollection implements Type
{
    public Type $entry;
    public iterable $container;

    public function __construct(Type $entry, iterable $container = [])
    {
        $this->entry = $entry;
        $this->container = $container;
    }

    public function getContainer(): iterable
    {
        return $this->container;
    }

    public function getEntry(): Type
    {
        return $this->entry;
    }
}