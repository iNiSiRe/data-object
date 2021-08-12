<?php

namespace inisire\DataObject\Definition;

class TEnum implements TPrimitive
{
    public function __construct(
        private Definition $type,
        private array $options,
        private bool $keyAsLabel = true
    ) {}

    /**
     * @return Definition
     */
    public function getType(): Definition
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function isKeyAsLabel(): bool
    {
        return $this->keyAsLabel;
    }
}