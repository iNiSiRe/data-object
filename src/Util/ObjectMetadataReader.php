<?php


namespace inisire\DataObject\Util;


use Doctrine\Common\Annotations\AnnotationReader;
use inisire\DataObject\Definition\Annotation\Ignore;
use inisire\DataObject\Definition\Annotation\Property;
use inisire\DataObject\Definition\Annotation\TViewProperty;
use inisire\DataObject\Definition\Definition;
use inisire\DataObject\Definition\IObject;
use inisire\DataObject\Definition\TBoolean;
use inisire\DataObject\Definition\TDateTime;
use inisire\DataObject\Definition\TInteger;
use inisire\DataObject\Definition\TNumber;
use inisire\DataObject\Definition\TObject;
use inisire\DataObject\Definition\TPartialObject;
use inisire\DataObject\Definition\TString;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyReadInfo;

class ObjectMetadataReader
{
    private AnnotationReader $annotationReader;
    private ReflectionExtractor $reflectionExtractor;

    public function __construct()
    {
        $this->annotationReader = new AnnotationReader();
        $this->reflectionExtractor = new ReflectionExtractor();
    }

    protected function guessPropertyDefinition(\ReflectionProperty $property): ?Definition
    {
        $type = $property->getType()?->getName();

        $definition = match ($type) {
            'string' => new TString(),
            'int' => new TInteger(),
            'float' => new TNumber(),
            'bool' => new TBoolean(),
            \DateTime::class, \DateTimeImmutable::class => new TDateTime(),
            default => null
        };

        if (!$definition && class_exists($type)) {
            $definition = new TObject($type);
        }

        return $definition;
    }

    private function isReadable(string $class, string $property): bool
    {
        return $this->reflectionExtractor->isReadable($class, $property);
    }

    private function isWritable(string $class, string $property): bool
    {
        return $this->reflectionExtractor->isWritable($class, $property);
    }

    /**
     * @param TObject|TPartialObject|object $object
     *
     * @return array<\inisire\DataObject\Definition\Property>
     */
    public function getProperties(object $object): array
    {
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
            $ignore = $this->annotationReader->getPropertyAnnotation($property, Ignore::class);

            if ($ignore) {
                continue;
            }

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
                $className = $object->getClass();
            } else {
                $className = $object::class;
            }

            $readable = $this->isReadable($className, $property->getName());
            $writable = $this->isWritable($className, $property->getName());

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

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $view = $this->annotationReader->getMethodAnnotation($method, TViewProperty::class);

            if (!$view) {
                continue;
            }

            $properties[] = new \inisire\DataObject\Definition\Property(
                $view->name,
                $view->definition,
                false,
                true,
                [
                    'read' => new PropertyReadInfo(PropertyReadInfo::TYPE_METHOD, $method->getName(), PropertyReadInfo::VISIBILITY_PUBLIC, false, false),
                    'write' => null
                ]
            );
        }

        return $properties;
    }
}
