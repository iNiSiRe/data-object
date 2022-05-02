<?php

namespace inisire\DataObject\Schema\Attribute;

use inisire\DataObject\Schema\Type\Type;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Property
{
    public function __construct(
        private Type $type
    )
    {
    }

    public function getType(): Type
    {
        return $this->type;
    }
}