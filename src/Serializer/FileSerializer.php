<?php

namespace inisire\DataObject\Serializer;

use inisire\DataObject\Definition\Definition;
use inisire\DataObject\Definition\TFile;
use inisire\DataObject\Errors;
use Symfony\Component\HttpFoundation\File\File;

class FileSerializer implements DataSerializerInterface
{
    /**
     * @param Definition $type
     * @param mixed      $data
     */
    public function serialize(Definition $type, mixed $data)
    {
        return $data;
    }

    public function deserialize(Definition $type, mixed $data, array &$errors = [])
    {
        if ($data instanceof File) {
            return $data;
        } else {
            $errors[] = Errors::create(Errors::INVALID_FILE);
            return null;
        }
    }

    public function isSupports(Definition $definition): bool
    {
        return $definition instanceof TFile;
    }
}