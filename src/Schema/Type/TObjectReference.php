<?php


namespace inisire\DataObject\Schema\Type;


use inisire\DataObject\Serializer\ObjectReferenceSerializer;

class TObjectReference implements Type
{
    public function __construct(
        private Type    $referenceType,
        private TObject $object,
        private string  $loaderName,
        private ?string  $managerName = null,
    )
    {
    }

    public function getReferenceType(): Type
    {
        return $this->referenceType;
    }

    public function getObject(): TObject
    {
        return $this->object;
    }

    public function getLoaderName(): string
    {
        return $this->loaderName;
    }

    public function getManagerName(): ?string
    {
        return $this->managerName;
    }

    public function getSerializer(): string
    {
        return ObjectReferenceSerializer::class;
    }
}