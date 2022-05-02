<?php

namespace inisire\DataObject\Runtime;

use inisire\DataObject\Schema\Property;

interface PropertyRuntimeInterface
{
    public function getSchema(): Property;
    public function getValue(): mixed;
    public function setValue(mixed $value): void;
    public function getErrors(): array;
}