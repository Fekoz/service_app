<?php


namespace App\Util\Tools;


class SystemUtil
{
    /**
     * @param array $array
     * @return object
     */
    public static function convertObjectInArray(array $array): object
    {
        $result = (object) $array;

        foreach ($result as $key => $value) {
            if (is_array($value)) {
                $result->{$key} = self::convertObjectInArray($value);
            }
        }

        return $result;
    }
}
