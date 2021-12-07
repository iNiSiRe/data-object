<?php

namespace inisire\DataObject\Definition\Annotation;

use inisire\DataObject\Definition\Definition;

/**
 * @Annotation
 */
class TViewProperty
{
    public string $name;

    public Definition $definition;
}