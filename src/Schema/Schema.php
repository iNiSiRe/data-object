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
    /**
     * @var array<Property>
     */
    private array $properties = [];

    public static function ofClassName(string $class): static
    {
        $reflectionExtractor = new ReflectionExtractor();

        $instance = new self();

        $classReflection = new \ReflectionClass($class);

        foreach ($classReflection->getProperties() as $propertyReflection) {
            if (AttributeExtractor::hasAttribute($propertyReflection, Attribute\IgnoreProperty::class)) {
                continue;
            }

            $attribute = AttributeExtractor::getFirstAttribute($propertyReflection, Attribute\Property::class);

            $type = $attribute?->getType();

            // Try to guess type
            if ($type === null && $propertyReflection->hasType()) {
                $type = TypeResolver::resolveByReflection($propertyReflection->getType());
            }

            if ($type === null) {
                continue;
            }

            $nullable = !$propertyReflection->hasType() || $propertyReflection->getType()->allowsNull();
            $readInfo = $reflectionExtractor->getReadInfo($class, $propertyReflection->getName());
            $writeInfo = $reflectionExtractor->getWriteInfo($class, $propertyReflection->getName(), ['enable_constructor_extraction' => false]);
            $default = $propertyReflection->getDefaultValue();

            if ($writeInfo && $writeInfo->getType() == 'none') {
                $writeInfo = null;
            }

            if (!$readInfo && !$writeInfo) {
                continue;
            }

            $instance->properties[] = new Property($propertyReflection->getName(), $type, $nullable, $default, $readInfo, $writeInfo);
        }

        foreach ($classReflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $attribute = AttributeExtractor::getFirstAttribute($method, Attribute\CalculatedProperty::class);

            if (!$attribute) {
                continue;
            }

            $readInfo = new PropertyReadInfo(PropertyReadInfo::TYPE_METHOD, $method->getName(), PropertyReadInfo::VISIBILITY_PUBLIC, false, false);

            $instance->properties[] = new Property($attribute->getName(), $attribute->getType(), false, null, $readInfo);
        }

        return $instance;
    }

    /**
     * @return Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}