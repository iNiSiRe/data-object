<?php


namespace inisire\DataObject\Runtime;


use inisire\DataObject\Schema\Type\TObjectReference;

interface ObjectLoaderInterface
{
    public function load(TObjectReference $type, mixed $id): ?object;

    public function getAlias(): string;
}