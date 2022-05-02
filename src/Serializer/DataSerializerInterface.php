<?php


namespace inisire\DataObject\Serializer;


use inisire\DataObject\Schema\Type\Type;

interface DataSerializerInterface
{
    public function serialize(Type $type, mixed $data);
    public function deserialize(Type $type, mixed $data, array &$errors = []);
    public function isSupports(Type $type): bool;
}