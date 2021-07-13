<?php


namespace inisire\DataObject\Definition;


/**
 * @phpstan-template TKey
 * @psalm-template TKey of array-key
 * @psalm-template T
 * @template-extends IteratorAggregate<TKey, T>
 * @template-extends ArrayAccess<TKey|null, T>
 */
class TCollection implements Definition
{
    public Definition $entry;
    public iterable $container;
    public int $min;
    public ?int $max;

    public function __construct(Definition $entry, iterable $container = [], int $min = 0, ?int $max = null)
    {
        $this->entry = $entry;
        $this->container = $container;
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return iterable
     */
    public function getContainer(): iterable
    {
        return $this->container;
    }

    /**
     * @return Definition
     */
    public function getEntry(): Definition
    {
        return $this->entry;
    }
}