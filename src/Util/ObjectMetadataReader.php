<?php


namespace inisire\DataObject\Util;


use Doctrine\Common\Annotations\AnnotationReader;
use inisire\DataObject\Definition\Annotation\Property;
use inisire\DataObject\Definition\Definition;
use inisire\DataObject\Definition\IObject;
use inisire\DataObject\Definition\TDateTime;
use inisire\DataObject\Definition\TInteger;
use inisire\DataObject\Definition\TNumber;
use inisire\DataObject\Definition\TObject;
use inisire\DataObject\Definition\TPartialObject;
use inisire\DataObject\Definition\TString;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class ObjectMetadataReader
{
    private AnnotationReader $annotationReader;
    private PropertyAccessor $propertyAccessor;

    public function __construct()
    {
        $this->annotationReader = new AnnotationReader();
        $this->propertyAccessor = new PropertyAccessor();
    }

    protected function guessPropertyDefinition(\ReflectionProperty $property): ?Definition
    {
        $type = $property->getType()->getName();

        $definition = match ($type) {
            'string' => new TString(),
            'int' => new TInteger(),
            'float' => new TNumber(),
            \DateTime::class => new TDateTime(),
            default => null
        };

        if (!$definition && class_exists($type)) {
            $definition = new TObject($type);
        }

        return $definition;
    }

    /**
     * @param object $object
     *
     * @return array<\inisire\DataObject\Definition\Property>
     */
    public function getProperties(object $object): array
    {
        if ($object instanceof IObject) {
            return $object->getObjectProperties();
        }

        if ($object instanceof TObject && is_a($object->getClass(), IObject::class, true)) {
            return $object->getClass()::getObjectProperties();
        }

        try {
            if ($object instanceof TObject || $object instanceof TPartialObject) {
                $class = new \ReflectionClass($object->getClass());
            } else {
                $class = new \ReflectionClass($object);
            }
        } catch (\ReflectionException $e) {
            return [];
        }

        $properties = [];
        foreach ($class->getProperties() as $property) {
            /**
             * @var Property $annotation
             */
            $annotation = $this->annotationReader->getPropertyAnnotation($property, Property::class);

            if ($annotation) {
                $definition = $annotation->definition;
            } else {
                $definition = $this->guessPropertyDefinition($property);
            }

            if (!$definition) {
                continue;
            }

            if ($object instanceof TObject) {
                $instance = $object->createInstance();
            } else {
                $instance = $object;
            }

            $readable = $this->propertyAccessor->isReadable($instance, $property->getName());
            $writable = $this->propertyAccessor->isWritable($instance, $property->getName());

            if ($readable === false && $writable === false) {
                continue;
            }

            $property = new \inisire\DataObject\Definition\Property(
                $property->getName(),
                $definition,
                $property->getType()->allowsNull(),
                $readable === true && $writable === false
            );

            $properties[] = $property;
        }

        return $properties;
    }
}