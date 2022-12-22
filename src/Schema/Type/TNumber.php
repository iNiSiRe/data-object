<?php

namespace inisire\DataObject\Schema\Type;


class TNumber extends TPrimitive implements \inisire\DataObject\OpenAPI\Type
{
    public function getSchema(): array
    {
        return [
            'type' => 'number'
        ];
    }
}