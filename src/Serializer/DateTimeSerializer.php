<?php


namespace inisire\DataObject\Serializer;


use inisire\DataObject\Definition\Definition;
use inisire\DataObject\Definition\TDateTime;
use inisire\DataObject\Error\Error;

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
            $errors[] = new Error('The value should be a string');
            return null;
        }

        $result = \DateTime::createFromFormat($type->getFormat(), $data);

        if ($result === false) {
            $errors[] = new Error(sprintf('The value should be valid date-time string formatted "%s"', $type->getFormat()));
            return null;
        }

        return $result;
    }

    public function isSupports(Definition $definition): bool
    {
        return $definition instanceof TDateTime;
    }
}