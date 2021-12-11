<?php

namespace App\Enums;

class FieldTypeEnum
{
    public const TYPE_STRING = 'string';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_INTEGER = 'integer';

    public static function getAllTypes(): array
    {
        return  [
            self::TYPE_BOOLEAN,
            self::TYPE_INTEGER,
            self::TYPE_STRING
        ];
    }
}
