<?php

namespace inisire\DataObject\Schema;

class AttributeExtractor
{
    /**
     * @template T
     *
     * @param \ReflectionProperty|\ReflectionMethod $reflection
     * @param class-string<T> $class
     *
     * @return T|null
     */
    public static function getFirstAttribute(\ReflectionProperty|\ReflectionMethod $reflection, string $class): ?object
    {
        $attribute = $reflection->getAttributes($class)[0] ?? null;

        return $attribute?->newInstance();
    }

    public static function hasAttribute(\ReflectionProperty $property, string $class): bool
    {
        return static::getFirstAttribute($property, $class) !== null;
    }
}