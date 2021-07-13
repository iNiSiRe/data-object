<?php


namespace inisire\DataObject\Definition;


interface ICollection extends Definition
{
    public function getEntry(): Definition;
}