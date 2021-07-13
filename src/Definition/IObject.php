<?php


namespace inisire\DataObject\Definition;


use inisire\DataObject\Definition\Property;

interface IObject extends Definition
{
    /**
     * @return Property[]
     */
    public static function getObjectProperties(): array;
}