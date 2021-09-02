<?php


namespace inisire\DataObject\Error;


class Error implements ErrorInterface
{
    private string $code;
    private ErrorMessage $message;

    public function __construct(ErrorMessage $message, string $code)
    {
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return ErrorMessage
     */
    public function getMessage(): ErrorMessage
    {
        return $this->message;
    }
}