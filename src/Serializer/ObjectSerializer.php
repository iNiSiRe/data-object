<?php

namespace inisire\DataObject\Serializer;

use inisire\DataObject\DataSerializerProvider;
use inisire\DataObject\Error\Errors;
use inisire\DataObject\Error\PropertyError;
use inisire\DataObject\Runtime\ObjectRuntime;
use inisire\DataObject\Schema\Property;
use inisire\DataObject\Schema\Schema;
use inisire\DataObject\Schema\Type\TObject;
use inisire\DataObject\Schema\Type\TPartialObject;
use inisire\DataObject\Schema\Type\TPolymorphObject;
use inisire\DataObject\Schema\Type\Type;
use inisire\DataObject\DataObjectWizard;
use Symfony\Component\PropertyInfo\PropertyReadInfo;

class ObjectSerializer implements DataSerializerInterface
{
    public function __construct(
        private DataSerializerProvider $provider
    )
    {
    }

    private function readValue(object $object, Property $property): mixed
    {
        $readInfo = $property->getReadInfo();
        $accessor = $readInfo->getName();

        try {
            $value = match ($readInfo->getType()) {
                PropertyReadInfo::TYPE_METHOD => $object->$accessor(),
                PropertyReadInfo::TYPE_PROPERTY => $object->$accessor,
                default => throw new \RuntimeException('Unsupported accessor type')
            };
        } catch (\Error $exception) {
            $value = null;
        }

        return $value;
    }

    public function serialize(Type $type, mixed $data)
    {
        if (!is_object($data)) {
            return null;
        }

        $serialized = [];

        foreach (ObjectRuntime::create($data, $this->provider)->getProperties() as $property) {
            $schema = $property->getSchema();
            $name = $schema->getName();

            if ($schema->getReadInfo() !== null) {
                $serialized[$name] = $property->getValue();
            } else {
                $serialized[$name] = null;
            }
        }

        if ($type instanceof TPolymorphObject && $serialized !== []) {
            $discriminator = $type->getDiscriminator();
            $serialized[$discriminator->getProperty()] = array_flip($discriminator->getMap())[$data::class] ?? null;
        }

        return $serialized;
    }

    public function deserialize(Type $type, mixed $data, array &$errors = [])
    {
        if ($data === null) {
            return null;
        }

        if (is_object($data) && is_a($data, $type->getClass(), true)) {
            return $data;
        }

        if (!is_array($data)) {
            $errors[] = Errors::create(Errors::IS_NOT_ARRAY);
            return null;
        }

        if ($type instanceof TObject) {
            $instance = $type->createInstance();
        } elseif ($type instanceof TPartialObject) {
            $instance = new \stdClass();
            $type = new TObject($type->getClass());
        } elseif ($type instanceof TPolymorphObject) {
            $discriminator = $type->getDiscriminator();
            $key = $data[$discriminator->getProperty()] ?? null;

            if ($key === null) {
                $errors[] = new PropertyError($discriminator->getProperty(), [Errors::create(Errors::IS_BLANK)]);
                return null;
            }

            $instance = $type->createInstance($key);

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

            $type = new TObject($instance::class);
        } else {
            throw new \InvalidArgumentException('Parameter $object should be TObject');
        }

        foreach (ObjectRuntime::create($instance, $this->provider)->getProperties() as $property) {
            $schema = $property->getSchema();
            $name = $schema->getName();

            if (!array_key_exists($name, $data)) {
                continue;
            }

            if ($schema->isReadOnly()) {
                $errors[] = new PropertyError($name, [Errors::create(Errors::IS_NOT_WRITABLE)]);
                continue;
            }

            $property->setValue($data[$name]);

            if (count($property->getErrors()) > 0) {
                $errors[] = new PropertyError($name, $property->getErrors());
            }
        }

        return $instance;
    }

    public function isSupports(Type $type): bool
    {
        return $type instanceof TObject || $type instanceof TPolymorphObject;
    }
}