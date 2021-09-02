<?php


namespace inisire\DataObject\Serializer;


use inisire\DataObject\Definition\Definition;
use inisire\DataObject\Definition\TDictionary;
use inisire\DataObject\Error\Error;
use inisire\DataObject\Errors;

class DictionarySerializer implements DataSerializerInterface
{
    public function serialize(Definition|TDictionary $type, mixed $data)
    {
        if ($data === null) {
            return null;
        }

        if (is_array($data)) {
            return $data;
        } else {
            throw new \RuntimeException('The value should be an array');
        }
    }

    public function deserialize(Definition|TDictionary $type, mixed $data, array &$errors = [])
    {
        if ($data === null) {
            return null;
        }

        if (is_array($data)) {
            return $data;
        } else {
            $errors[] = Errors::create(Errors::INVALID_DICTIONARY);
            return null;
        }
    }

    public function isSupports(Definition $definition): bool
    {
        return $definition instanceof TDictionary;
    }
}