<?php

namespace inisire\DataObject;

use inisire\DataObject\Schema\Type\Type;
use inisire\DataObject\Serializer\CollectionSerializer;
use inisire\DataObject\Serializer\DataSerializerInterface;
use inisire\DataObject\Serializer\DateTimeSerializer;
use inisire\DataObject\Serializer\DictionarySerializer;
use inisire\DataObject\Serializer\ObjectSerializer;
use inisire\DataObject\Serializer\ScalarSerializer;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DataObjectWizard
{
    public function __construct(
        private DataSerializerProvider $provider
    )
    {
    }

    public function transform(Type $type, mixed $data): mixed
    {
        $serializer = $this->provider->getByType($type);

        if (!$serializer) {
            throw new \RuntimeException(sprintf('Serializer for the type "%s" not exists', $type::class));
        }

        return $serializer->serialize($type, $data);
    }

    public function map(Type $type, mixed $data, array &$errors = [])
    {
        $serializer = $this->provider->getByType($type);

        if (!$serializer) {
            throw new \RuntimeException(sprintf('Serializer for the type "%s" not exists', $type::class));
        }

        return $serializer->deserialize($type, $data, $errors);
    }

    public function patch(object $object, object $changes): array
    {
        $accessor = new PropertyAccessor();
        $patch = [];

        foreach (get_object_vars($changes) as $property => $value) {
            $previousValue = $accessor->getValue($object, $property);
            if ($value !== $previousValue) {
                $accessor->setValue($object, $property, $value);
                $patch[$property] = [$previousValue, $value];
            }
        }

        return $patch;
    }
}