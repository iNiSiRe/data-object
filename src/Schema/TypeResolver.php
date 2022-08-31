<?php

namespace inisire\DataObject\Schema;

use inisire\DataObject\Schema\Type\TBoolean;
use inisire\DataObject\Schema\Type\TBuiltinEnum;
use inisire\DataObject\Schema\Type\TDateTime;
use inisire\DataObject\Schema\Type\TInteger;
use inisire\DataObject\Schema\Type\TNumber;
use inisire\DataObject\Schema\Type\TObject;
use inisire\DataObject\Schema\Type\TString;
use inisire\DataObject\Schema\Type\TUuid;
use inisire\DataObject\Schema\Type\Type;
use Symfony\Component\Uid\Uuid;

class TypeResolver
{
    public static function resolveByReflection(\ReflectionNamedType $type): ?Type
    {
        $typeName = $type->getName();

        $resolved = match ($typeName) {
            'string' => new TString(),
            'int' => new TInteger(),
            'float' => new TNumber(),
            'bool' => new TBoolean(),
            \DateTime::class, \DateTimeImmutable::class => new TDateTime(),
            Uuid::class => new TUuid(),
            default => null
        };

        if ($resolved !== null) {
            return $resolved;
        }

        if (enum_exists($typeName)) {
            return new TBuiltinEnum($typeName);
        }

        if (class_exists($typeName)) {
            return new TObject($typeName);
        }

        return null;
    }
}