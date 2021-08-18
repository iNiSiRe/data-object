<?php

namespace inisire\DataObject\Definition\Annotation;

/**
 * @Annotation 
 */
class TFile extends \inisire\DataObject\Definition\TFile
{
    public function __construct($options)
    {
        parent::__construct($options['mimetypes'] ?? []);
    }
}