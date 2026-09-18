<?php

namespace inisire\DataObject\Serializer;

use inisire\DataObject\DataSerializerProvider;
use inisire\DataObject\Error\Errors;
use inisire\DataObject\Schema\Type\TDictionary;
use inisire\DataObject\Schema\Type\Type;

class DictionarySerializer implements DataSerializerInterface
{
    public function __construct(
        private DataSerializerProvider $provider,
    ) {
    }

    public function serialize(Type|TDictionary $type, mixed $data)
    {
        if (null === $data) {
            return null;
        }

        if (!is_array($data)) {
            throw new \RuntimeException('The value should be an array');
        }

        if (null === $entryType = $type->getEntry()) {
            return $data;
        }

        $serializer = $this->provider->getByType($entryType);

        $container = [];
        foreach ($data as $key => $item) {
            $container[$key] = $serializer->serialize($entryType, $item);
        }

        return $container;
    }

    public function deserialize(Type|TDictionary $type, mixed $data, array &$errors = [])
    {
        if (null === $data) {
            return null;
        }

        if (!is_array($data)) {
            $errors[] = Errors::create(Errors::INVALID_DICTIONARY);

            return null;
        }

        if (null === $entryType = $type->getEntry()) {
            return $data;
        }

        $serializer = $this->provider->getByType($entryType);
        $container = [];

        foreach ($data as $key => $item) {
            if (null === $mappedItem = $serializer->deserialize($entryType, $item, $errors)) {
                continue;
            }

            $container[$key] = $mappedItem;
        }

        return $container;
    }
}
