<?php


namespace inisire\DataObject\Util;


use inisire\DataObject\Definition\Definition;
use inisire\DataObject\Definition\IObject;
use inisire\DataObject\Definition\TCollection;
use inisire\DataObject\Definition\TInteger;
use inisire\DataObject\Definition\TNumber;
use inisire\DataObject\Definition\TPolymorphObject;
use inisire\DataObject\Definition\TObject;
use inisire\DataObject\Definition\TPrimitive;
use inisire\DataObject\Definition\TString;
use inisire\DataObject\Serializer\DataSerializerInterface;
use inisire\DataObject\Serializer\DateTimeSerializer;
use inisire\DataObject\Serializer\DictionarySerializer;
use inisire\DataObject\Serializer\ScalarSerializer;
use Symfony\Component\PropertyAccess\PropertyAccessor;


class DataTransformer
{
    private PropertyAccessor $accessor;

    private ObjectMetadataReader $metadataReader;

    /**
     * @var array<DataSerializerInterface>
     */
    private array $serializers = [];

    public function __construct()
    {
        $this->accessor = new PropertyAccessor(throw: PropertyAccessor::DO_NOT_THROW);
        $this->metadataReader = new ObjectMetadataReader();
        $this->serializers = [
            new ScalarSerializer(),
            new DictionarySerializer(),
            new DateTimeSerializer()
        ];
    }

    public function registerSerializer(DataSerializerInterface $serializer)
    {
        $this->serializers[] = $serializer;
    }

    public function primitive(mixed $data, Definition $type): mixed
    {
        foreach ($this->serializers as $serializer) {
            if (!$serializer->isSupports($type)) {
                continue;
            }

            return $serializer->serialize($type, $data);
        }

        throw new \RuntimeException('Serializer for the type not exists');
    }

    public function object(object $object, ?TObject $type = null): ?array
    {
        if ($object === null) {
            return null;
        }

        $container = [];

        foreach ($this->metadataReader->getProperties($object) as $property) {
            $propertyName = $property->getName();

            if ($this->accessor->isReadable($object, $propertyName)) {
                $data = $this->accessor->getValue($object, $propertyName);
                $container[$propertyName] = $this->any($data, $property->getType());
            } else {
                $container[$propertyName] = null;
            }
        }

        return $container;
    }

    protected function guessDefinition(mixed $data): ?Definition
    {
        return match (true) {
            is_string($data) => new TString(),
            is_int($data) => new TInteger(),
            is_float($data) => new TNumber(),
            default => null
        };
    }

    public function collection(?iterable $data, ?TCollection $type = null): ?iterable
    {
        if ($data === null) {
            return null;
        }

        $container = [];

        foreach ($data as $item) {
            if ($type) {
                $container[] = $this->any($item, $type->getEntry());
            } else {
                $container[] = is_object($item)
                    ? $this->object($item)
                    : $this->any($item, $this->guessDefinition($item));
            }
        }

        return $container;
    }

    public function any(mixed $data, Definition $type): mixed
    {
        if ($type instanceof TObject) {
            return $this->object($data, $type);
        } elseif ($type instanceof TCollection) {
            return $this->collection($data, $type);
        } else {
            return $this->primitive($data, $type);
        }
    }
}