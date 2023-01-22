<?php

namespace inisire\DataObject\OpenAPI;

class Specification
{
    private array $schemas = [];

    private array $paths = [];

    public function addPath(string $method, string $path, array $parameters = [], array $request = [],
                            array $responses = [], array $tags = [], string $summary = '')
    {
        $schema = [
            'tags' => $tags,
            'summary' => $summary,
            'parameters' => $parameters
        ];

        if (!empty($request)) {
            $schema['requestBody'] = (object) $request;
        }

        if (!empty($responses)) {
            $schema['responses'] = (object) $responses;
        }

        $this->paths[$path][strtolower($method)] = $schema;
    }

    public function addSchema(string $name, array $schema)
    {
        $this->schemas[$name] = $schema;
    }

    public function hasSchema(string $name): bool
    {
        return array_key_exists($name, $this->schemas);
    }

    public function getSchemaRef(string $name): ?array
    {
        $schema = $this->schemas[$name] ?? null;

        if ($schema) {
            return [
                '$ref' => sprintf('#/components/schemas/%s', $name)
            ];
        } else {
            return null;
        }
    }

    public function toArray(): array
    {
        return [
            "openapi" => "3.0.0",
            "info" => [
                "title" => "API",
                "description" => "",
                "version" => "1.0.0"
            ],
            'servers' => [],
            'tags' => [],
            'paths' => $this->paths,
            'components' => ['schemas' => $this->schemas]
        ];
    }
}