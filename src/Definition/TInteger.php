<?php


namespace inisire\DataObject\Definition;


class TInteger implements TPrimitive
{
    public ?int $default;
    public ?int $min;
    public ?int $max;

    public function __construct(int $default = null, int $min = null, int $max = null)
    {
        $this->default = $default;
        $this->min = $min;
        $this->max = $max;
    }
}