<?php


namespace inisire\DataObject\Definition;


class TDateTime implements TPrimitive
{
    private string $format;

    public function __construct(string $format = DATE_ATOM)
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }
}