<?php

namespace Lynxcat\Annotation\Util;

class Util
{
    /**
     * Converts an array to a string, and does not support a value type of object
     * @param array $arr
     * @return string
     */
    public static function arrayToString(array $arr)
    {
        return str_replace(
            ['{', '}', ':', ','],
            ['[', ']', ' => ', ', '],
            json_encode($arr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }
}
