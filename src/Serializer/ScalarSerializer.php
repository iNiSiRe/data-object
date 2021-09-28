<?php


namespace inisire\DataObject\Serializer;


use inisire\DataObject\Definition\Definition;
use inisire\DataObject\Definition\TBoolean;
use inisire\DataObject\Definition\TEnum;
use inisire\DataObject\Definition\TInteger;
use inisire\DataObject\Definition\TMixed;
use inisire\DataObject\Definition\TNumber;
use inisire\DataObject\Definition\TString;
use inisire\DataObject\Error\Error;
use inisire\DataObject\Errors;

class ScalarSerializer implements DataSerializerInterface
{
    private const OPTIONS_BY_DEFINITION = [
        TNumber::class => [FILTER_VALIDATE_FLOAT],
        TInteger::class => [FILTER_VALIDATE_INT],
        TString::class => [FILTER_UNSAFE_RAW],
        TBoolean::class => [FILTER_VALIDATE_BOOL]
    ];

    private const OPTIONS_BY_TYPENAME = [
        'string' => self::OPTIONS_BY_DEFINITION[TString::class],
        'integer' => self::OPTIONS_BY_DEFINITION[TInteger::class],
        'float' => self::OPTIONS_BY_DEFINITION[TNumber::class],
        'bool' => self::OPTIONS_BY_DEFINITION[TBoolean::class]
    ];

    protected function filter(Definition $definition, mixed $data, array &$errors = [])
    {
        $options = self::OPTIONS_BY_DEFINITION[$definition::class]
            ?? self::OPTIONS_BY_TYPENAME[gettype($data)]
            ?? [FILTER_UNSAFE_RAW];

        $filtered = filter_var($data, $options[0], ($options[1] ?? null) | FILTER_NULL_ON_FAILURE);

        if ($data !== null && $filtered === null) {
            $errors[] = Errors::create(Errors::INVALID_SCALAR);
            return null;
        } else {
            return $filtered;
        }
    }

    public function serialize(Definition $type, mixed $data)
    {
        if ($type instanceof TEnum) {
            return $type->isKeyAsLabel() ? array_flip($type->getOptions())[$data] : $data;
        } else {
            return $data;
        }
    }

    public function deserialize(Definition $type, mixed $data, array &$errors = [])
    {
        if ($type instanceof TEnum) {
            $filteredData = $this->filter($type->getType(), $data, $errors);

            if ($type->isKeyAsLabel()) {
                $map = $type->getOptions();
                if (!in_array($filteredData, array_keys($map))) {
                    $errors[] = Errors::create(Errors::INVALID_ENUM);
                    $filteredData = null;
                } else {
                    $filteredData = $map[$filteredData];
                }
            } else {
                $enum = $type->getOptions();
                if (!in_array($filteredData, $enum)) {
                    $errors[] = Errors::create(Errors::INVALID_ENUM);
                    $filteredData = null;
                }
            }
        } else {
            $filteredData = $this->filter($type, $data, $errors);
        }

        return $filteredData;
    }

    public function isSupports(Definition $definition): bool
    {
        return $definition instanceof TString
            || $definition instanceof TNumber
            || $definition instanceof TInteger
            || $definition instanceof TMixed
            || $definition instanceof TEnum
            || $definition instanceof TBoolean;
    }
}