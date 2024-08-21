<?php

namespace inisire\DataObject\Runtime\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use inisire\DataObject\Runtime\ObjectLoaderInterface;
use inisire\DataObject\Schema\Type\TObjectReference;

class EntityManagerObjectLoader implements ObjectLoaderInterface
{
    public function __construct(
        private ?ManagerRegistry $doctrine
    )
    {
    }

    public function load(TObjectReference $type, mixed $id): ?object
    {
        return $this->doctrine->getRepository($type->getObject()->getClass(), $type->getManagerName())->find($id);
    }

    public function getAlias(): string
    {
        return 'orm';
    }
}