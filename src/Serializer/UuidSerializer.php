<?php

namespace inisire\DataObject\Serializer;

use inisire\DataObject\Error\Errors;
use inisire\DataObject\Schema\Type\TUuid;
use inisire\DataObject\Schema\Type\Type;
use Symfony\Component\Uid\Uuid;

class UuidSerializer implements DataSerializerInterface
{
    public function serialize(Type $type, mixed $data)
    {
        if ($data === null) {
            return null;
        }

        if ($data instanceof Uuid === false) {
            return null;
        }

        return $data->toRfc4122();
    }

    public function deserialize(Type $type, mixed $data, array &$errors = [])
    {
        if ($data === null) {
            return null;
        }

        if (is_string($data) === false) {
            $errors[] = Errors::create(Errors::IS_NOT_STRING);
            return null;
        }

        return Uuid::fromString($data);
    }
}