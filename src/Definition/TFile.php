<?php

namespace inisire\DataObject\Definition;

class TFile implements Definition
{
    public function __construct(
        private array $mimetypes
    ) {}
    
    public function getMimetypes(): array
    {
        return $this->mimetypes;
    }
}