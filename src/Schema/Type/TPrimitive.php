<?php


namespace inisire\DataObject\Schema\Type;


use inisire\DataObject\Serializer\ScalarSerializer;

abstract class TPrimitive implements Type
{
    public function getSerializer(): string
    {
        return ScalarSerializer::class;
    }
}