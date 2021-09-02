<?php


namespace inisire\DataObject\Serializer;


use inisire\DataObject\Definition\Definition;
use inisire\DataObject\Definition\TDateTime;
use inisire\DataObject\Error\Error;
use inisire\DataObject\Errors;

class DateTimeSerializer implements DataSerializerInterface
{
    public function serialize(Definition|TDateTime $type, mixed $data)
    {
        if ($data === null) {
            return null;
        }

        if ($data instanceof \DateTimeInterface === false) {
            return null;
        }

        return $data->format($type->getFormat());
    }

    public function deserialize(Definition|TDateTime $type, mixed $data, array &$errors = [])
    {
        if ($data === null) {
            return null;
        }

        if (is_string($data) === false) {
            $errors[] = Errors::create(Errors::IS_NOT_STRING);
            return null;
        }

        $result = \DateTime::createFromFormat($type->getFormat(), $data);

        if ($result === false) {
            $errors[] = Errors::create(Errors::INVALID_DATETIME, ['{{format}}' => $type->getFormat()]);
            return null;
        }

        return $result;
    }

    public function isSupports(Definition $definition): bool
    {
        return $definition instanceof TDateTime;
    }
}