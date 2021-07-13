<?php


namespace inisire\DataObject\Definition\Annotation;

use inisire\DataObject\Definition\Definition;

/**
 * @Annotation
 */
class Property
{
    public Definition $definition;

    public function __construct(array $options)
    {
        $this->definition = $options['definition'] ?? $options['value'];
    }
}