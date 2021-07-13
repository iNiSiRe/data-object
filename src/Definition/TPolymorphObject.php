<?php


namespace inisire\DataObject\Definition;


use inisire\DataObject\VO\Discriminator;

class TPolymorphObject implements Definition
{
    private string $class;
    private Discriminator $discriminator;

    public function __construct(string $class, Discriminator $discriminator)
    {
        $this->class = $class;
        $this->discriminator = $discriminator;
    }

    /**
     * @return string
     */
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

    /**
     * @return Discriminator
     */
    public function getDiscriminator(): Discriminator
    {
        return $this->discriminator;
    }
}