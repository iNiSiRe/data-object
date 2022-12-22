<?php


namespace inisire\DataObject\Schema\Type;


interface Type
{
    public function getSerializer(): string;
}