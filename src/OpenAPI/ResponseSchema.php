<?php

namespace inisire\DataObject\OpenAPI;

use inisire\DataObject\Schema\Type\Type;

class ResponseSchema
{
    public function __construct(
        private int $statusCode,
        private string $contentType,
        private Type $schema
    )
    {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
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