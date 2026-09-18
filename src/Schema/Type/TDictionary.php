<?php

namespace inisire\DataObject\Schema\Type;

use inisire\DataObject\Serializer\DictionarySerializer;

class TDictionary extends TPrimitive
{
    public ?Type $entry;

    public function __construct(?Type $entry = null)
    {
        $this->entry = $entry;
    }

    public function getEntry(): ?Type
    {
        return $this->entry;
    }

    public function getSerializer(): string
    {
        return DictionarySerializer::class;
    }
}
