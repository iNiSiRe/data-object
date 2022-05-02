<?php

namespace inisire\DataObject\OpenAPI;

use inisire\DataObject\Schema\Type\Type;

class RequestSchema
{
    public function __construct(
        private string $contentType,
        private Type $schema
    )
    {
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getSchema(): Type
    {
        return $this->schema;
    }
}