<?php

namespace inisire\DataObject;

use inisire\DataObject\Error\Error;
use inisire\DataObject\Error\ErrorInterface;
use inisire\DataObject\Error\ErrorMessage;

class Errors
{
    public const IS_NOT_STRING = '1ec0406f-fe2e-64ea-9747-a3097e0d772f';
    public const INVALID_DATETIME = '1ec04098-04f5-6fbc-b696-3f74cdc21a50';
    public const INVALID_DICTIONARY = '1ec040a7-739b-6e76-b7ac-d16a7ce464a6';
    public const INVALID_FILE = '1ec040b9-996d-66d8-977d-99a45b7e5d3e';
    public const INVALID_OBJECT_REFERENCE = '1ec040bc-a4c4-6948-896e-93e4bb918984';
    public const INVALID_SCALAR = '1ec040c8-9743-6d12-882e-89234ca322a1';
    public const INVALID_ENUM = '1ec040c8-d1c3-6dde-b70a-d9b7bea2f197';
    public const INVALID_COLLECTION = '1ec04161-d86f-6180-91a5-af4fa04bbd33';
    public const IS_NOT_WRITABLE = '';
    public const INVALID_DISCRIMINATOR = '';
    public const IS_BLANK = '';
    public const IS_NOT_ARRAY = '';

    protected const MESSAGES = [
        self::IS_NOT_STRING            => 'This value should be a string',
        self::INVALID_DATETIME         => 'This value should be a valid date-time string formatted "{{format}}"',
        self::INVALID_DICTIONARY       => 'This value should be an array',
        self::INVALID_FILE             => 'This value should be a file',
        self::INVALID_OBJECT_REFERENCE => 'This value should be an existing reference',
        self::INVALID_SCALAR           => 'This value should be a valid scalar',
        self::INVALID_ENUM             => 'This value should be in enum',
        self::INVALID_COLLECTION       => 'This value should be a collection',
        self::IS_NOT_WRITABLE          => 'This property has read only access',
        self::INVALID_DISCRIMINATOR    => 'This value should be a valid discriminator. Available: {{values}}',
        self::IS_BLANK                 => 'This value should not be blank',
        self::IS_NOT_ARRAY             => 'This value should be an array'
    ];

    public static function create(string $code, array $parameters = []): ErrorInterface
    {
        return new Error(new ErrorMessage(self::MESSAGES[$code], $parameters), $code);
    }
}