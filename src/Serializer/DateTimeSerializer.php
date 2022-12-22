<?php


namespace inisire\DataObject\Serializer;


use inisire\DataObject\Schema\Type\Type;
use inisire\DataObject\Schema\Type\TDateTime;
use inisire\DataObject\Error\Error;
use inisire\DataObject\Error\Errors;

class DateTimeSerializer implements DataSerializerInterface
{
    public function serialize(Type|TDateTime $type, mixed $data)
    {
        if ($data === null) {
            return null;
        }

        if ($data instanceof \DateTimeInterface === false) {
            return null;
        }

        return $data->format($type->getFormat());
    }

    public function deserialize(Type|TDateTime $type, mixed $data, array &$errors = [])
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
}