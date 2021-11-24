<?php


namespace inisire\DataObject\Util;


use Doctrine\ODM\MongoDB\DocumentManager;
use inisire\DataObject\Definition\TObjectReference;

class MongoDocumentLoader implements ObjectLoaderInterface
{
    public function __construct(
        private ?DocumentManager $manager = null
    )
    {
    }

    public function load(TObjectReference $definition, mixed $id): ?object
    {
        return $this->manager->getRepository($definition->object->getClass())->find($id);
    }

    public function getAlias(): string
    {
        return 'odm';
    }
}