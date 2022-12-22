<?php

namespace inisire\DataObject\Serializer;

use inisire\DataObject\Schema\Type\Type;
use inisire\DataObject\Schema\Type\TFile;
use inisire\DataObject\Error\Errors;
use Symfony\Component\HttpFoundation\File\File;

class FileSerializer implements DataSerializerInterface
{
    /**
     * @param Type $type
     * @param mixed $data
     */
    public function serialize(Type $type, mixed $data)
    {
        return $data;
    }

    public function deserialize(Type $type, mixed $data, array &$errors = [])
    {
        if (empty($data)) {
            return null;
        }

        if (!$data instanceof File) {
            $errors[] = Errors::create(Errors::INVALID_FILE);
            return null;
        }

        return $data;
    }
}