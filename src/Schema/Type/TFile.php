<?php

namespace inisire\DataObject\Schema\Type;

class TFile implements Type
{
    public function __construct(
        private array $mimetypes
    ) {}
    
    public function getMimetypes(): array
    {
        return $this->mimetypes;
    }
}