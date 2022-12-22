<?php

namespace inisire\DataObject\Schema\Type;


class TDictionary extends TPrimitive implements \inisire\DataObject\OpenAPI\Type
{
    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => ['type' => 'string']
        ];
    }
}