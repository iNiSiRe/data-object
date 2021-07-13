<?php


namespace inisire\DataObject\Definition;


class TObjectReference implements Definition
{
    public Definition $reference;
    public TObject $object;
    public string $loader;
}