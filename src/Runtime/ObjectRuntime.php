<?php

namespace inisire\DataObject\Runtime;

use inisire\DataObject\DataObjectWizard;
use inisire\DataObject\DataSerializerProvider;
use inisire\DataObject\Schema\Schema;


class ObjectRuntime
{
    /**
     * @var array<PropertyRuntimeInterface>
     */
    private array $properties = [];

    public static function create(object $object, DataSerializerProvider $provider): static
    {
        $instance = new self();

        foreach (Schema::ofClassName($object::class)->getProperties() as $property) {
            $instance->properties[] = new PropertyRuntime($property, $object, $provider->getByType($property->getType()));
        }

        return $instance;
    }

    /**
     * @return PropertyRuntimeInterface[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}