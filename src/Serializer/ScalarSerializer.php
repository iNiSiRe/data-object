<?php


namespace inisire\DataObject\Serializer;


use inisire\DataObject\Schema\Type\Type;
use inisire\DataObject\Schema\Type\TBoolean;
use inisire\DataObject\Schema\Type\TEnum;
use inisire\DataObject\Schema\Type\TInteger;
use inisire\DataObject\Schema\Type\TMixed;
use inisire\DataObject\Schema\Type\TNumber;
use inisire\DataObject\Schema\Type\TString;
use inisire\DataObject\Error\Errors;

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

    protected function filter(Type $type, mixed $data, array &$errors = [])
    {
        if ($data === null || $data === '') {
            return null;
        }

        $options = self::OPTIONS_BY_DEFINITION[$type::class]
            ?? self::OPTIONS_BY_TYPENAME[gettype($data)]
            ?? [FILTER_UNSAFE_RAW];

        $filtered = filter_var($data, $options[0], ($options[1] ?? null) | FILTER_NULL_ON_FAILURE);

        if ($filtered === null) {
            $errors[] = Errors::create(Errors::INVALID_SCALAR);
            return null;
        } else {
            return $filtered;
        }
    }

    public function serialize(Type $type, mixed $data)
    {
        if ($type instanceof TEnum) {
            if ($data === null) {
                return null;
            }

            return $type->isKeyAsLabel() ? array_flip($type->getOptions())[$data] : $data;
        } else {
            return $data;
        }
    }

    public function deserialize(Type $type, mixed $data, array &$errors = [])
    {
        if ($type instanceof TEnum) {
            if ($data === null) {
                return null;
            }

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

    public function isSupports(Type $type): bool
    {
        return $type instanceof TString
            || $type instanceof TNumber
            || $type instanceof TInteger
            || $type instanceof TMixed
            || $type instanceof TEnum
            || $type instanceof TBoolean;
    }
}