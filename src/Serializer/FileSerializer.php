<?php

namespace inisire\DataObject\Serializer;

use inisire\DataObject\Definition\Definition;
use inisire\DataObject\Definition\TFile;
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
            $errors[] = sprintf('File should be instance of "%s"', File::class);
            return null;
        }
    }

    public function isSupports(Definition $definition): bool
    {
        return $definition instanceof TFile;
    }
}