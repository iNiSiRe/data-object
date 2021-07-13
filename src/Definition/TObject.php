<?php


namespace inisire\DataObject\Definition;


class TObject implements Definition
{
    private string $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    public function createInstance(): object
    {
        return new $this->class();
    }
}