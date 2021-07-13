<?php


namespace inisire\DataObject\Util;


use inisire\DataObject\Definition\TObjectReference;

interface ObjectLoaderInterface
{
    public function load(TObjectReference $definition, mixed $id): ?object;
}