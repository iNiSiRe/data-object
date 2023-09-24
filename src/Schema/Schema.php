<?php

namespace inisire\DataObject\Schema;

use inisire\DataObject\Schema\Attribute as Attribute;
use inisire\DataObject\Schema\Type\TBoolean;
use inisire\DataObject\Schema\Type\TBuiltinEnum;
use inisire\DataObject\Schema\Type\TDateTime;
use inisire\DataObject\Schema\Type\TInteger;
use inisire\DataObject\Schema\Type\TNumber;
use inisire\DataObject\Schema\Type\TObject;
use inisire\DataObject\Schema\Type\TString;
use inisire\DataObject\Schema\Type\Type;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyReadInfo;

class Schema
{
    private function __construct(
        private \ReflectionClass $reflection
    )
    {
    }

    /**
     * @return iterable<\ReflectionProperty>
     */
    private function extractClassProperties(\ReflectionClass $class): iterable
    {
        foreach ($class->getProperties() as $property) {
            yield $property;
        }

        while ($class->getParentClass() !== false) {
            $class = $class->getParentClass();

            foreach ($class->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
                yield $property;
            }
        }
    }

    public static function ofClassName(string $class): static
    {
        return new static(new \ReflectionClass($class));
    }

    /**
     * @return iterable<Property>
     */
    public function getProperties(): iterable
    {
        $reflectionExtractor = new ReflectionExtractor();

        $classReflection = $this->reflection;

        foreach ($this->extractClassProperties($classReflection) as $propertyReflection) {
            if (AttributeExtractor::hasAttribute($propertyReflection, Attribute\IgnoreProperty::class)) {
                continue;
            }

            $attribute = AttributeExtractor::getFirstAttribute($propertyReflection, Attribute\Property::class);

            $type = $attribute?->getType();

            // Try to guess type
            if ($type === null && $propertyReflection->hasType() && $propertyReflection->getType() instanceof \ReflectionNamedType) {
                $type = TypeResolver::resolveByReflection($propertyReflection->getType());
            }

            if ($type === null) {
                continue;
            }

            $nullable = !$propertyReflection->hasType() || $propertyReflection->getType()->allowsNull();
            $readInfo = $reflectionExtractor->getReadInfo($classReflection->getName(), $propertyReflection->getName());
            $writeInfo = $reflectionExtractor->getWriteInfo($classReflection->getName(), $propertyReflection->getName(), ['enable_constructor_extraction' => false]);
            $default = $propertyReflection->getDefaultValue();

            if ($writeInfo && $writeInfo->getType() == 'none') {
                $writeInfo = null;
            }

            if (!$readInfo && !$writeInfo) {
                continue;
            }

            yield new Property($propertyReflection->getName(), $type, $nullable, $default, $readInfo, $writeInfo);
        }

        foreach ($classReflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $attribute = AttributeExtractor::getFirstAttribute($method, Attribute\CalculatedProperty::class);

            if (!$attribute) {
                continue;
            }

            $readInfo = new PropertyReadInfo(PropertyReadInfo::TYPE_METHOD, $method->getName(), PropertyReadInfo::VISIBILITY_PUBLIC, false, false);

            yield new Property($attribute->getName(), $attribute->getType(), false, null, $readInfo);
        }
    }
}
