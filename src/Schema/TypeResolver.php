<?php

namespace inisire\DataObject\Schema;

use inisire\DataObject\Schema\Type\TBoolean;
use inisire\DataObject\Schema\Type\TBuiltinEnum;
use inisire\DataObject\Schema\Type\TDateTime;
use inisire\DataObject\Schema\Type\TInteger;
use inisire\DataObject\Schema\Type\TNumber;
use inisire\DataObject\Schema\Type\TObject;
use inisire\DataObject\Schema\Type\TString;
use inisire\DataObject\Schema\Type\Type;

class TypeResolver
{
    public static function resolveByReflection(\ReflectionNamedType $type): ?Type
    {
        $typeName = $type->getName();

        if ($type->isBuiltin()) {
            return match ($typeName) {
                'string' => new TString(),
                'int' => new TInteger(),
                'float' => new TNumber(),
                'bool' => new TBoolean(),
                default => null
            };
        }

        if ($typeName == \DateTime::class) {
            return new TDateTime();
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