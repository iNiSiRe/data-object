<?php

namespace inisire\DataObject\Schema\Type;

use inisire\DataObject\Serializer\UuidSerializer;

class TUuid implements Type, \inisire\DataObject\OpenAPI\Type
{
    public function getSerializer(): string
    {
        return UuidSerializer::class;
    }

    public function getSchema(): array
    {
        return [
            'type' => 'string',
            'format' => 'uuid'
        ];
    }
}