<?php


namespace inisire\DataObject\Schema;


class Discriminator
{
    private string $property;

    /**
     * @var array<string, string>
     */
    private array $map;

    public function __construct(string $property, array $map = [])
    {
        $this->map = $map;
        $this->property = $property;
    }

    /**
     * @return string[]
     */
    public function getMap(): array
    {
        return $this->map;
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}