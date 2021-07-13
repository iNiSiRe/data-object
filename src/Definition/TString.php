<?php


namespace inisire\DataObject\Definition;


class TString implements TPrimitive
{
    public ?string $default;
    public int $minLength;
    public ?int $maxLength;

    public function __construct(string $default = null, int $minLength = 0, int $maxLength = null)
    {
        $this->default = $default;
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }
}