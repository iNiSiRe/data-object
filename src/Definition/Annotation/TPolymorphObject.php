<?php


namespace inisire\DataObject\Definition\Annotation;


use inisire\DataObject\VO\Discriminator;

/**
 * @Annotation
 */
class TPolymorphObject extends \inisire\DataObject\Definition\TPolymorphObject
{
    public function __construct($options)
    {
        parent::__construct($options['class'], $options['discriminator']);
    }
}