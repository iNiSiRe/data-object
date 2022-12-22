<?php

namespace inisire\DataObject\Schema\Type;


class TBoolean extends TPrimitive implements \inisire\DataObject\OpenAPI\Type
{
    public function getSchema(): array
    {
        return [
            'type' => 'number'
        ];
    }
}