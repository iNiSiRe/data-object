<?php

namespace inisire\DataObject\Schema\Type;

use inisire\DataObject\Serializer\FileSerializer;

class TFile implements Type, \inisire\DataObject\OpenAPI\Type
{
    public function __construct() {}

    public function getSerializer(): string
    {
        return FileSerializer::class;
    }

    public function getSchema(): array
    {
        return [
            'type' => 'string',
            'format' => 'binary'
        ];
    }
}