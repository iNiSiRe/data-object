<?php

namespace inisire\DataObject\Schema\Type;

class TBuiltinEnum implements Type
{
    public function __construct(
        private string $enum
    )
    {
    }

    public function getEnum(): string
    {
        return $this->enum;
    }
}