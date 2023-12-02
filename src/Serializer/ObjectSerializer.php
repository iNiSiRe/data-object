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
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\PropertyReadInfo;

class ObjectSerializer implements DataSerializerInterface
{
    /**
     * @var \SplObjectStorage<ObjectRuntime>
     */
    private \SplObjectStorage $objects;

    private PropertyAccessor $accessor;
    
    public function __construct(
        private DataSerializerProvider $provider
    )
    {
        $this->accessor = new PropertyAccessor();
        $this->objects = new \SplObjectStorage();
    }

    private function createObjectRuntime(object $object, Type $type): ObjectRuntime
    {
        if ($this->objects->contains($object)) {
            return $this->objects[$object];
        }
        
        if ($type instanceof TPolymorphObject) {
            // TODO: Validate $data::class belongs to discriminator map
            $runtimeSchema = ObjectRuntime::create($object, Schema::ofClassName($object::class), $this->provider);
        } else {
            $runtimeSchema = ObjectRuntime::create($object, Schema::ofClassName($type->getClass()), $this->provider);
        }
        
        $this->objects[$object] = $runtimeSchema;
        
        return $runtimeSchema;
    }
    
    public function serialize(Type|TObject|TPartialObject|TPolymorphObject $type, mixed $data)
    {
        if (!is_object($data)) {
            return null;
        }

        $serialized = [];

        $runtimeSchema = $this->createObjectRuntime($data, $type);

        foreach ($runtimeSchema->getProperties() as $property) {
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

    private function createInstance(TObject $type): ?object
    {
        $reflection = new \ReflectionClass($type->getClass());

        if ($reflection->getConstructor() === null || $reflection->getConstructor()->getNumberOfRequiredParameters() === 0) {
            $instance = new ($type->getClass())();
        } else {
            $instance = $reflection->newInstanceWithoutConstructor();
        }

        if (method_exists($instance, '__wakeup')) {
            $instance->__wakeup();
        }

        return $instance;
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
            $instance = $this->createInstance($type);
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

        $runtime = ObjectRuntime::create($instance, Schema::ofClassName($type->getClass()), $this->provider);

        foreach ($runtime->getProperties() as $property) {
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
}