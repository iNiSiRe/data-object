<?php


namespace inisire\DataObject\Runtime\Loader;


use Doctrine\ODM\MongoDB\DocumentManager;
use inisire\DataObject\Runtime\ObjectLoaderInterface;
use inisire\DataObject\Schema\Type\TObjectReference;

class MongoDocumentLoader implements ObjectLoaderInterface
{
    public function __construct(
        private DocumentManager $manager
    )
    {
    }

    public function load(TObjectReference $type, mixed $id): ?object
    {
        return $this->manager->getRepository($type->getObject()->getClass())->find($id);
    }

    public function getAlias(): string
    {
        return 'odm';
    }
}