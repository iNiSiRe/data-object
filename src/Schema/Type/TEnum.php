<?php

namespace inisire\DataObject\Schema\Type;


class TEnum extends TPrimitive
{
    public function __construct(
        private Type  $type,
        private array $options,
        private bool  $keyAsLabel = true
    ) {}

    public function getType(): Type
    {
        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function isKeyAsLabel(): bool
    {
        return $this->keyAsLabel;
    }
}