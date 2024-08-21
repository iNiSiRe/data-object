<?php

namespace inisire\DataObject\Schema\Type;

// TODO
class TBuiltinEnum
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