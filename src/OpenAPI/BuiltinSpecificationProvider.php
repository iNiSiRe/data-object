<?php

namespace inisire\DataObject\OpenAPI;

use inisire\DataObject\Schema\Property;
use inisire\DataObject\Schema\Type\TBoolean;
use inisire\DataObject\Schema\Type\TDateTime;
use inisire\DataObject\Schema\Type\TDictionary;
use inisire\DataObject\Schema\Type\TFile;
use inisire\DataObject\Schema\Type\TInteger;
use inisire\DataObject\Schema\Type\TNumber;
use inisire\DataObject\Schema\Type\TString;
use inisire\DataObject\Schema\Type\Type;


class BuiltinSpecificationProvider implements SpecificationProviderInterface
{
    private const TYPE_MAP = [
        TNumber::class => ['type' => 'number'],
        TString::class => ['type' => 'string'],
        TInteger::class => ['type' => 'integer'],
        TDateTime::class => [
            'type' => 'string',
            'format' => 'date-time'
        ],
        TDictionary::class => [
            'type' => 'object',
            'additionalProperties' => ['type' => 'string']
        ],
        TFile::class => [
                'type' => 'string',
                'format' => 'binary'
        ],
        TBoolean::class => ['type' => 'boolean']
    ];

    public function isTypeSupported(Type $type): bool
    {
        return in_array($type::class, array_keys(self::TYPE_MAP));
    }

    public function createTypeSchema(Type $type): array
    {
        return self::TYPE_MAP[$type::class] ?? [];
    }
}