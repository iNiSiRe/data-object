<?php


namespace inisire\DataObject\Schema\Type;


use inisire\DataObject\Schema\Discriminator;
use inisire\DataObject\Serializer\ObjectSerializer;

class TPolymorphObject implements Type
{
    private string $class;
    private Discriminator $discriminator;

    public function __construct(string $class, Discriminator $discriminator)
    {
        $this->class = $class;
        $this->discriminator = $discriminator;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getClassByDiscriminator(int|string $discriminator): ?string
    {
        return $this->getDiscriminator()->getMap()[$discriminator] ?? null;
    }

    public function createInstance(int|string $key): ?object
    {
        $class = $this->getClassByDiscriminator($key);

        if (!$class) {
            return null;
        }

        return new $class();
    }

    public function getDiscriminator(): Discriminator
    {
        return $this->discriminator;
    }

    public function getSerializer(): string
    {
        return ObjectSerializer::class;
    }
}