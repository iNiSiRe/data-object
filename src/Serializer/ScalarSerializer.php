<?php


namespace inisire\DataObject\Serializer;


use inisire\DataObject\Definition\Definition;
use inisire\DataObject\Definition\TInteger;
use inisire\DataObject\Definition\TMixed;
use inisire\DataObject\Definition\TNumber;
use inisire\DataObject\Definition\TString;
use inisire\DataObject\Error\Error;

class ScalarSerializer implements DataSerializerInterface
{
    private const OPTIONS_BY_DEFINITION = [
        TNumber::class => [FILTER_VALIDATE_FLOAT],
        TInteger::class => [FILTER_VALIDATE_INT],
        TString::class => [FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH],
    ];

    private const OPTIONS_BY_TYPENAME = [
        'string' => self::OPTIONS_BY_DEFINITION[TString::class],
        'integer' => self::OPTIONS_BY_DEFINITION[TInteger::class],
        'float' => self::OPTIONS_BY_DEFINITION[TNumber::class],
    ];

    protected function filter(Definition $definition, mixed $data, array &$errors = [])
    {
        $options = self::OPTIONS_BY_DEFINITION[$definition::class]
            ?? self::OPTIONS_BY_TYPENAME[gettype($data)]
            ?? [FILTER_UNSAFE_RAW];

        $data = filter_var($data, $options[0], $options[1] ?? null);

        if ($data === false) {
            $errors[] = new Error('The value should be valid');
            return null;
        } else {
            return $data;
        }
    }

    public function serialize(Definition $type, mixed $data)
    {
        return $data;
    }

    public function deserialize(Definition $type, mixed $data, array &$errors = [])
    {
        return $this->filter($type, $data, $errors);
    }

    public function isSupports(Definition $definition): bool
    {
        return $definition instanceof TString
            || $definition instanceof TNumber
            || $definition instanceof TInteger
            || $definition instanceof TMixed;
    }
}