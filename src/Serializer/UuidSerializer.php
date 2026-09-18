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
        if ($data === null || $data === '') {
            return null;
        }

        if (is_string($data) === false) {
            $errors[] = Errors::create(Errors::IS_NOT_STRING);
            return null;
        }

        if (!Uuid::isValid($data)) {
            $errors[] = Errors::create(Errors::INVALID_UUID);
            return null;
        }

        $class = $type->getClass();
        if (!$class::isValid($data)) {
            $errors[] = Errors::create(Errors::INVALID_UUID_VERSION);
            return null;
        }

        return $class::fromString($data);
    }
}