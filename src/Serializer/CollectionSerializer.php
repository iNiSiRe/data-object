<?php

namespace inisire\DataObject\Serializer;

use inisire\DataObject\DataSerializerProvider;
use inisire\DataObject\Error\Errors;
use inisire\DataObject\Schema\Type\TCollection;
use inisire\DataObject\Schema\Type\Type;
use inisire\DataObject\DataObjectWizard;

class CollectionSerializer implements DataSerializerInterface
{
    public function __construct(
        private DataSerializerProvider $provider
    )
    {
    }

    public function serialize(Type|TCollection $type, mixed $data)
    {
        if ($data === null || $data === []) {
            return $data;
        }

        $serializer = $this->provider->getByType($type->getEntry());

        $container = $type->getContainer();

        foreach ($data as $item) {
            $container[] = $serializer->serialize($type->getEntry(), $item);
        }

        return $container;
    }

    public function deserialize(Type $type, mixed $data, array &$errors = [])
    {
        if ($data === null) {
            return null;
        }

        if (!is_array($data)) {
            $errors[] = Errors::create(Errors::INVALID_COLLECTION);
            return null;
        }

        $container = $type->getContainer();
        $entryType = $type->getEntry();
        $serializer = $this->provider->getByType($entryType);

        foreach ($data as $item) {
            $mappedItem = $serializer->deserialize($entryType, $item, $errors);

            if ($mappedItem === null) {
                continue;
            }

            $container[] = $mappedItem;
        }

        return $container;
    }
}