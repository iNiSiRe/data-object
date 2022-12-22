<?php


namespace inisire\DataObject\OpenAPI;


use inisire\DataObject\Schema\Schema;
use inisire\DataObject\Schema\Type\Type;
use inisire\DataObject\Schema\Type\TCollection;
use inisire\DataObject\Schema\Type\TEnum;
use inisire\DataObject\Schema\Type\TMixed;
use inisire\DataObject\Schema\Type\TObjectReference;
use inisire\DataObject\Schema\Type\TPartialObject;
use inisire\DataObject\Schema\Type\TPolymorphObject;
use inisire\DataObject\Schema\Type\TObject;


class SpecificationBuilder
{
    private Specification $specification;

    public function __construct()
    {
        $this->specification = new Specification();
    }

    private function createMixedSchema(TMixed $type): array
    {
        $schemas = [];
        foreach ($type->getTypes() as $definition) {
            $schemas[] = $this->createTypeSpecification($definition);
        }
        return [
            'oneOf' => $schemas
        ];
    }

    private function createTypeSpecification(Type $type)
    {
        if ($type instanceof TObject) {
            return $this->createObjectSchema($type);
        } elseif ($type instanceof TCollection) {
            return $this->createCollectionSchema($type);
        } elseif ($type instanceof TPolymorphObject) {
            return $this->createPolymorphObjectSchema($type);
        } elseif ($type instanceof TMixed) {
            return $this->createMixedSchema($type);
        } elseif ($type instanceof TPartialObject) {
            return $this->createObjectSchema(new TObject($type->getClass()));
        } elseif ($type instanceof TObjectReference) {
            return $this->createTypeSpecification($type->getReferenceType());
        } elseif ($type instanceof TEnum) {
            $schema = $this->createTypeSpecification($type->getType());
            $schema['enum'] = $type->isKeyAsLabel() ? array_keys($type->getOptions()) : $type->getOptions();
            return $schema;
        }

        if (!$type instanceof \inisire\DataObject\OpenAPI\Type) {
            throw new \RuntimeException(sprintf('Unsupported type "%s"', $type::class));
        }

        return $type->getSchema();
    }

    public function createObjectSchema(TObject $type): array
    {
        $parts = explode('\\', $type->getClass());
        $name = end($parts);

        if ($this->specification->hasSchema($name)) {
            return $this->specification->getSchemaRef($name);
        }

        $properties = [];
        $required = [];

        foreach (Schema::ofClassName($type->getClass())->getProperties() as $property) {
            $propertyType = $property->getType();

            $schema = $this->createTypeSpecification($propertyType);

            if ($property->isNullable()) {
                $schema['nullable'] = $property->isNullable();
            }

            if ($property->isReadOnly()) {
                $schema['readOnly'] = $property->isReadOnly();
            }

            if (null !== $default = $property->getDefault()) {
                $schema['default'] = $default;
            }

            if ($property->isRequired()) {
                $required[] = $property->getName();
            }

            $properties[$property->getName()] = $schema;
        }

        $schema = [
            'type' => 'object',
            'properties' => $properties,
            'required' => $required
        ];

        $this->specification->addSchema($name, $schema);

        return $this->specification->getSchemaRef($name);
    }

    private function createCollectionSchema(TCollection $type): array
    {
        return [
            'type' => 'array',
            'items' => $this->createTypeSpecification($type->getEntry())
        ];
    }

    private function createPolymorphObjectSchema(TPolymorphObject $type): array
    {
        return [
            'oneOf' => array_map(
                fn(string $class) => $this->createObjectSchema(new TObject($class)),
                array_values($type->getDiscriminator()->getMap())
            ),
            'discriminator' => [
                'propertyName' => $type->getDiscriminator()->getProperty(),
                'mapping' => array_map(
                    fn(string $class) => $this->createObjectSchema(new TObject($class))['$ref'],
                    $type->getDiscriminator()->getMap()
                )
            ]
        ];
    }

    /**
     * @param array<ResponseSchema> $responses
     */
    private function createResponsesSchema(array $responses): array
    {
        $result = [];

        foreach ($responses as $response) {
            $schema = $result[$response->getStatusCode()] ?? ['description' => '', 'content' => []];

            $schema['content'][$response->getContentType()] = [
                'schema' => $this->createTypeSpecification($response->getSchema())
            ];

            $result[$response->getStatusCode()] = $schema;
        }

        return $result;
    }

    public function addPath(string $method, string $path, RequestSchema $request = null, array $responses = [], array $tags = [], string $description = "")
    {
        $parametersSchema = [];
        $requestBodySchema = [];

        if ($request) {
            if ($method === 'GET') {
                $parametersSchema = [
                    [
                        'in' => 'query',
                        'name' => 'query',
                        'schema' => $this->createTypeSpecification($request->getSchema())
                    ]
                ];
            } else {
                $requestBodySchema = [
                    'content' => [
                        $request->getContentType() => [
                            'schema' => $this->createTypeSpecification($request->getSchema())
                        ]
                    ]
                ];
            }
        }

        $responsesSchema = $this->createResponsesSchema($responses);

        $this->specification->addPath($method, $path, $parametersSchema, $requestBodySchema, $responsesSchema, $tags, $description);
    }

    public function getSpecification(): Specification
    {
        return $this->specification;
    }
}