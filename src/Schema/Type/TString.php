<?php


namespace inisire\DataObject\Schema\Type;


class TString extends TPrimitive implements \inisire\DataObject\OpenAPI\Type
{
    public function getSchema(): array
    {
        return [
            'type' => 'string'
        ];
    }
}