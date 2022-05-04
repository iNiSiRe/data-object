<?php


namespace inisire\DataObject\Serializer;


use inisire\DataObject\Schema\Type\Type;
use inisire\DataObject\Schema\Type\TDictionary;
use inisire\DataObject\Error\Errors;

class DictionarySerializer implements DataSerializerInterface
{
    public function serialize(Type|TDictionary $type, mixed $data)
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

    public function deserialize(Type|TDictionary $type, mixed $data, array &$errors = [])
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

    public function isSupports(Type $type): bool
    {
        return $type instanceof TDictionary;
    }
}