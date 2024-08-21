<?php

namespace inisire\DataObject\Schema\Attribute;

use inisire\DataObject\Schema\Type\Type;

#[\Attribute(\Attribute::TARGET_METHOD)]
class CalculatedProperty
{
    public function __construct(
        private string $name,
        private Type $type
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): Type
    {
        return $this->type;
    }
}