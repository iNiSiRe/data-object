<?php

namespace inisire\DataObject\Schema\Type;


use inisire\DataObject\Serializer\DictionarySerializer;

class TDictionary extends TPrimitive implements \inisire\DataObject\OpenAPI\Type
{
    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => ['type' => 'string']
        ];
    }

    public function getSerializer(): string
    {
        return DictionarySerializer::class;
    }
}