<?php


namespace inisire\DataObject\Util;


use inisire\CQRS\Schema\Json;
use inisire\CQRS\Schema\FormData;
use inisire\CQRS\Schema\Schema;
use inisire\CQRS\Schema\Stream;
use inisire\DataObject\Definition\Definition;
use inisire\DataObject\Definition\TBoolean;
use inisire\DataObject\Definition\TCollection;
use inisire\DataObject\Definition\TDateTime;
use inisire\DataObject\Definition\TDictionary;
use inisire\DataObject\Definition\TEnum;
use inisire\DataObject\Definition\TFile;
use inisire\DataObject\Definition\TMixed;
use inisire\DataObject\Definition\TObjectReference;
use inisire\DataObject\Definition\TPartialObject;
use inisire\DataObject\Definition\TPolymorphObject;
use inisire\DataObject\Definition\TInteger;
use inisire\DataObject\Definition\TNumber;
use inisire\DataObject\Definition\TObject;
use inisire\DataObject\Definition\TString;

class DocGenerator
{
    private ObjectMetadataReader $metadataReader;

    /**
     * @var array<callable>
     */
    private array $generators = [];

    private array $schemas = [];

    private array $paths = [];

    public function __construct()
    {
        $this->metadataReader = new ObjectMetadataReader();

        $this->generators = [
            TNumber::class          => function (TNumber $type) {
                $schema = ['type' => 'number'];
                if ($type->default !== null) {
                    $schema['default'] = $type->default;
                }
                return $schema;
            },
            TString::class          => function (TString $type) {
                $schema = ['type' => 'string'];
                if ($type->default !== null) {
                    $schema['default'] = $type->default;
                }
                return $schema;
            },
            TInteger::class         => function (TInteger $type) {
                $schema = ['type' => 'integer'];
                if ($type->default !== null) {
                    $schema['default'] = $type->default;
                }
                return $schema;
            },
            TDateTime::class        => fn(TDateTime $type) => [
                'type'   => 'string',
                'format' => 'date-time'
            ],
            TDictionary::class      => fn(TDictionary $type) => [
                'type'                 => 'object',
                'additionalProperties' => [
                    'type' => 'string'
                ]
            ],
            TObject::class          => function (TObject $type) {
                $properties = [];
                $required = [];
                foreach ($this->metadataReader->getProperties($type) as $property) {

                    if ($property->getType() instanceof TObject) {
                        $schema = $this->getObjectSchemaReference($property->getType());
                        if ($property->isReadOnly()) {
                            $schema = [
                                'allOf'    => [$schema],
                                'readOnly' => $property->isReadOnly()
                            ];
                        }
                    } else {
                        $schema = $this->createSchema($property->getType());
//                        $schema['nullable'] = $property->isAllowNull();
                        if ($property->isReadOnly()) {
                            $schema['readOnly'] = $property->isReadOnly();
                        }
                    }

                    if ($property->isRequired()) {
                        $required[] = $property->getName();
                    }

                    $properties[$property->getName()] = $schema;
                }

                return [
                    'type'       => 'object',
                    'properties' => $properties,
                    'required'   => $required
                ];
            },
            TPolymorphObject::class => fn(TPolymorphObject $type) => [
                'oneOf'         => array_map(
                    fn(string $class) => $this->getObjectSchemaReference(new TObject($class)),
                    array_values($type->getDiscriminator()->getMap())
                ),
                'discriminator' => [
                    'propertyName' => $type->getDiscriminator()->getProperty(),
                    'mapping'      => array_map(
                        fn(string $class) => $this->getObjectSchemaReference(new TObject($class))['$ref'],
                        $type->getDiscriminator()->getMap()
                    )
                ]
            ],
            TCollection::class      => fn(TCollection $type) => [
                'type'  => 'array',
                'items' => $type->getEntry() instanceof TObject
                    ? $this->getObjectSchemaReference($type->getEntry())
                    : $this->createSchema($type->getEntry())
            ],
            TObjectReference::class => fn(TObjectReference $type) => $this->createSchema($type->reference),
            TPartialObject::class   => fn(TPartialObject $type) => $this->getObjectSchemaReference(new TObject($type->getClass())),
            TMixed::class           => function (TMixed $type) {
                $schemas = [];
                foreach ($type->getDefinitions() as $definition) {
                    $schemas[] = $this->createSchema($definition);
                }
                return [
                    'oneOf' => $schemas
                ];
            },
            TEnum::class            => function (TEnum $type) {
                $schema = $this->createSchema($type->getType());
                $schema['enum'] = $type->isKeyAsLabel() ? array_keys($type->getOptions()) : $type->getOptions();
                return $schema;
            },
            TFile::class            => function (TFile $type) {
                return [
                    'type'   => 'string',
                    'format' => 'binary'
                ];
            },
            TBoolean::class         => fn(TBoolean $type) => [
                'type' => 'boolean'
            ]
        ];
    }

    protected function resolveGenerator(Definition $type): ?callable
    {
        foreach ($this->generators as $class => $generator) {
            if ($type instanceof $class) {
                return $generator;
            }
        }

        return null;
    }

    public function createSchema(Definition $type)
    {
        $generator = $this->resolveGenerator($type);

        if (!$generator) {
            throw new \RuntimeException(sprintf('The type "%s" is not supported', $type::class));
        }

        return $generator($type);
    }

    public function getObjectSchemaReference(TObject $type)
    {
        $parts = explode('\\', $type->getClass());
        $name = end($parts);

        if (!isset($this->schemas[$name])) {
            $this->schemas[$name] = $this->createSchema($type);
        }

        return [
            '$ref' => sprintf('#/components/schemas/%s', $name)
        ];
    }

    public function addPath(string $method, string $path, Schema $request, Schema $response, array $tags = [], string $description = "")
    {
        $parametersSchema = [];
        $requestBodySchema = [];

        if ($request instanceof Json || $request instanceof FormData) {
            $schema = $request->schema instanceof TObject
                ? $this->getObjectSchemaReference($request->schema)
                : $this->createSchema($request->schema);

            if ($method === 'GET') {
                $parametersSchema = [[
                                         'in'     => 'query',
                                         'name'   => 'query',
                                         'schema' => $schema
                                     ]];
            } else {
                $requestBodySchema = [
                    'content' => [
                        $request->getContentType() => [
                            'schema' => $schema
                        ]
                    ]
                ];
            }
        }

        if ($response instanceof Json) {
            $content = [
                $response->getContentType() => [
                    'schema' => $response->schema instanceof TObject
                        ? $this->getObjectSchemaReference($response->schema)
                        : $this->createSchema($response->schema)
                ]
            ];
        } elseif ($response instanceof Stream) {
            $content = [
                $response->getContentType() => [
                    'schema' => [
                        'type'   => 'string',
                        'format' => 'binary'
                    ]
                ]
            ];
        } else {
            throw new \InvalidArgumentException(sprintf('Unsupported output "%s"', $response::class));
        }

        $schema = [
            'tags'    => $tags,
            'summary' => $description
        ];

        if ($parametersSchema) {
            $schema['parameters'] = $parametersSchema;
        }

        if ($requestBodySchema) {
            $schema['requestBody'] = $requestBodySchema;
        }

        if ($content) {
            $schema['responses'] = [
                '200' => [
                    'description' => '',
                    'content' => $content
                ]
            ];
        }

        $this->paths[$path][strtolower($method)] = $schema;
    }

    /**
     * @return array
     */
    public function getDoc(): array
    {
        return [
            "openapi"    => "3.0.0",
            "info"       => [
                "title"       => "API",
                "description" => "",
                "version"     => "1.0.0"
            ],
            'servers'    => [],
            'tags'       => [],
            'paths'      => $this->paths,
            'components' => ['schemas' => $this->schemas]
        ];
    }
}