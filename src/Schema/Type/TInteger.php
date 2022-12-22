<?php


namespace inisire\DataObject\Schema\Type;


class TInteger extends TPrimitive implements \inisire\DataObject\OpenAPI\Type
{
    public function getSchema(): array
    {
        return [
            'type' => 'integer'
        ];
    }
}