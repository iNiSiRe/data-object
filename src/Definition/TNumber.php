<?php


namespace inisire\DataObject\Definition;


class TNumber implements TPrimitive
{
    public ?float $default;
    public ?float $min;
    public ?float $max;

    public function __construct(float $default = null, float $min = null, float $max = null)
    {
        $this->default = $default;
        $this->min = $min;
        $this->max = $max;
    }
}