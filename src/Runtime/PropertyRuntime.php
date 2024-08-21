<?php

namespace inisire\DataObject\Runtime;


use inisire\DataObject\Schema\Property;
use inisire\DataObject\Schema\Type\Type;
use inisire\DataObject\Serializer\DataSerializerInterface;
use Symfony\Component\PropertyInfo\PropertyReadInfo;
use Symfony\Component\PropertyInfo\PropertyWriteInfo;

class PropertyRuntime implements PropertyRuntimeInterface
{
    private array $errors = [];

    private bool $serializing = false;
    
    public function __construct(
        protected Property $schema,
        protected object $object,
        protected DataSerializerInterface $serializer
    )
    {
    }

    public function getValue(): mixed
    {
        $readInfo = $this->schema->getReadInfo();
        $accessor = $readInfo->getName();

        try {
            $value = match ($readInfo->getType()) {
                PropertyReadInfo::TYPE_METHOD => $this->object->$accessor(),
                PropertyReadInfo::TYPE_PROPERTY => $this->object->$accessor,
                default => throw new \RuntimeException('Unsupported accessor type')
            };
        } catch (\Error) {
            return null;
        }
        
        if ($this->serializing) {
            throw new \RuntimeException(sprintf('Detected cyclic serialization for %s::%s', $this->object::class, $this->schema->getName()));
        }
        
        $this->serializing = true;
        $result = $this->serializer->serialize($this->schema->getType(), $value);
        $this->serializing = false;
        
        return $result;
    }

    public function setValue(mixed $value): void
    {
        $data = $this->serializer->deserialize($this->schema->getType(), $value, $this->errors);

        if ($data === null && $this->schema->isNullable() === false) {
            // TODO: This probably should be an error
            return;
        }

        if ($this->object instanceof \stdClass) {
            $this->object->{$this->schema->getName()} = $data;
        } else {
            $writeInfo = $this->schema->getWriteInfo();

            switch ($writeInfo->getType()) {
                case PropertyWriteInfo::TYPE_METHOD:
                    $this->object->{$writeInfo->getName()}($data);
                    break;
                case PropertyWriteInfo::TYPE_PROPERTY:
                    $this->object->{$writeInfo->getName()} = $data;
                    break;
                default:
                    throw new \RuntimeException(sprintf('Unsupported mutator type "%s"', $writeInfo->getType()));
            }
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getSchema(): Property
    {
        return $this->schema;
    }
}
