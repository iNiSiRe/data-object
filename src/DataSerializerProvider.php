<?php

namespace inisire\DataObject;

use inisire\DataObject\Schema\Type\Type;
use inisire\DataObject\Serializer\CollectionSerializer;
use inisire\DataObject\Serializer\DataSerializerInterface;
use inisire\DataObject\Serializer\DateTimeSerializer;
use inisire\DataObject\Serializer\DictionarySerializer;
use inisire\DataObject\Serializer\ObjectSerializer;
use inisire\DataObject\Serializer\ScalarSerializer;

class DataSerializerProvider
{
    /**
     * @var array<string, DataSerializerInterface>
     */
    private array $serializers = [];

    /**
     * @param array<DataSerializerInterface> $serializers
     */
    public function __construct(array $serializers = [])
    {
        $this->add($serializers);
    }

    public function add(DataSerializerInterface|array $serializer)
    {
        $serializers = (array) $serializer;

        foreach ($serializers as $serializer) {
            $this->serializers[$serializer::class] = $serializer;
        }
    }

    public function getByName(string $class): ?DataSerializerInterface
    {
        return $this->serializers[$class] ?? null;
    }

    public function getByType(Type $type): ?DataSerializerInterface
    {
        foreach ($this->serializers as $serializer) {
            if ($serializer->isSupports($type)) {
                return $serializer;
            }
        }

        return null;
    }
}