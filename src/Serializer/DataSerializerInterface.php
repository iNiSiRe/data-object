<?php


namespace inisire\DataObject\Serializer;


use inisire\DataObject\Definition\Definition;

interface DataSerializerInterface
{
    public function serialize(Definition $type, mixed $data);
    public function deserialize(Definition $type, mixed $data, array &$errors = []);
    public function isSupports(Definition $definition): bool;
}