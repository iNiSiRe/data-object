<?php


namespace inisire\DataObject\Util;


use inisire\DataObject\Definition\Definition;
use inisire\DataObject\Definition\TCollection;
use inisire\DataObject\Definition\TPartialObject;
use inisire\DataObject\Definition\TPolymorphObject;
use inisire\DataObject\Error\Error;
use inisire\DataObject\Errors;
use inisire\DataObject\Serializer\DataSerializerInterface;
use inisire\DataObject\Serializer\DateTimeSerializer;
use inisire\DataObject\Serializer\DictionarySerializer;
use inisire\DataObject\Serializer\FileSerializer;
use inisire\DataObject\Serializer\ScalarSerializer;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use inisire\DataObject\Definition\IObject;
use inisire\DataObject\Definition\TObject;
use inisire\DataObject\Error\BulkError;
use inisire\DataObject\Error\PropertyError;


class DataMapper
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
            new DateTimeSerializer(),
            new FileSerializer()
        ];
    }

    public function create(TObject $type, array $data, array &$errors = []): object
    {
        return $this->object($type->createInstance(), $data, $errors);
    }

    public function registerSerializer(DataSerializerInterface $serializer)
    {
        $this->serializers[] = $serializer;
    }

    /**
     * @param object $object
     *
     * @return array<\inisire\DataObject\Definition\Property>
     */
    protected function getObjectProperties(object $object): array
    {
        return $this->metadataReader->getProperties($object);
    }

    public function object(object|string $definition, mixed $data, array &$errors = []): ?object
    {
        if ($data === null) {
            return null;
        }

        if (is_string($definition) && class_exists($definition)) {
            $definition = new TObject($definition);
        }

        if (is_object($data) && is_a($data, $definition->getClass(), true)) {
            return $data;
        }

        if (!is_array($data)) {
            $errors[] = Errors::create(Errors::IS_NOT_ARRAY);
            return null;
        }

        if ($definition instanceof TObject) {
            $instance = $definition->createInstance();
        } elseif ($definition instanceof TPartialObject) {
            $instance = new \stdClass();
            $definition = new TObject($definition->getClass());
        } elseif ($definition instanceof TPolymorphObject) {
            $discriminator = $definition->getDiscriminator();
            $key = $data[$discriminator->getProperty()] ?? null;

            if ($key === null) {
                $errors[] = new PropertyError($discriminator->getProperty(), [Errors::create(Errors::IS_BLANK)]);
                return null;
            }

            $instance = $definition->createInstance($key);

            if ($instance === null) {
                $errors[] = new PropertyError(
                    $discriminator->getProperty(),
                    [
                        Errors::create(Errors::INVALID_DISCRIMINATOR, [
                            '{{values}}', implode(', ', array_keys($discriminator->getMap()))
                        ])
                    ]
                );
                return null;
            }

            $definition = new TObject($instance::class);
        } else {
            throw new \InvalidArgumentException('Parameter $object should be TObject');
        }

        foreach ($this->getObjectProperties($definition) as $property) {
            $propertyName = $property->getName();

            if (!array_key_exists($propertyName, $data)) {
                continue;
            }

            if ($property->isReadOnly()) {
                $errors[] = new PropertyError($property->getName(), [Errors::create(Errors::IS_NOT_WRITABLE)]);
                continue;
            }

            $transformErrors = [];
            $transformedData = $this->any($property->getType(), $data[$propertyName], $transformErrors);

            if (count($transformErrors) > 0) {
                $errors[] = new PropertyError($property->getName(), $transformErrors);
            }

            if ($transformedData === null && $property->isAllowNull() === false) {
                continue;
            }

            if ($instance instanceof \stdClass) {
                $instance->$propertyName = $transformedData;
            } elseif ($this->accessor->isWritable($instance, $propertyName)) {
                $this->accessor->setValue($instance, $propertyName, $transformedData);
            }
        }

        return $instance;
    }

    public function collection(TCollection $type, mixed $data, array &$errors = []): ?iterable
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

        foreach ($data as $item) {
            $mappedItem = $this->any($entryType, $item, $errors);

            if ($mappedItem === null) {
                continue;
            }

            $container[] = $mappedItem;
        }

        return $container;
    }

    public function any(Definition $type, mixed $data, array &$errors = []): mixed
    {
        if ($type instanceof TObject || $type instanceof TPolymorphObject || $type instanceof TPartialObject) {
            return $this->object($type, $data, $errors);
        } elseif ($type instanceof TCollection) {
            return $this->collection($type, $data, $errors);
        } else {
            return $this->primitive($type, $data, $errors);
        }
    }

    protected function primitive(Definition $type, mixed $data, array &$errors = [])
    {
        foreach ($this->serializers as $serializer) {
            if (!$serializer->isSupports($type)) {
                continue;
            }

            return $serializer->deserialize($type, $data, $errors);
        }

        throw new \RuntimeException(sprintf('Serializer for the type "%s" not exists', $type::class));
    }

    public function subtract(IObject $object, IObject $exclude): array
    {

    }

    public function unmap(IObject $object): array
    {

    }

    public function serializeObject(IObject $object): array
    {

    }
}