<?php


namespace inisire\DataObject\Definition\Annotation;

/**
 * @Annotation
 */
class Discriminator extends \inisire\DataObject\VO\Discriminator
{
    public function __construct($options)
    {
        parent::__construct($options['property'], $options['map']);
    }
}