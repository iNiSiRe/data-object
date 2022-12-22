<?php

namespace inisire\DataObject\Schema\Type;

use inisire\DataObject\Serializer\DateTimeSerializer;


class TDateTime implements Type, \inisire\DataObject\OpenAPI\Type
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

    public function getSchema(): array
    {
        return [
            'type' => 'string',
            'format' => 'date-time'
        ];
    }

    public function getSerializer(): string
    {
        return DateTimeSerializer::class;
    }
}