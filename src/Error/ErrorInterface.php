<?php


namespace inisire\DataObject\Error;


interface ErrorInterface
{
    public function getCode(): string;
    public function getMessage(): ErrorMessage;
}