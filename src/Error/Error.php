<?php


namespace inisire\DataObject\Error;


class Error implements ErrorInterface
{
    private string $message;
    private string $description;

    public function __construct(string $message, string $description = '')
    {
        $this->message = $message;
        $this->description = $description;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'description' => $this->description
        ];
    }
}