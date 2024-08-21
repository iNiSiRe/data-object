<?php

namespace inisire\DataObject\OpenAPI;

interface Type
{
    public function getSchema(): array;
}