<?php

namespace App\Util;

class ArrayUtils
{
    /**
     * Input: $myarray = array('a', 'b', array(array(array('x'), 'y', 'z')), array(array('p')));
     * Result: Array
     * (
     *  [0] => a
     *  [1] => b
     *  [2] => x
     *  [3] => y
     *  [4] => z
     *  [5] => p
     * )
     *
     * @param $array
     * @param array $result
     * @return array|mixed
     */
    public static function array_flatten($array, array $result = array())
    {
        foreach ($array as $x => $xValue) {
            if (is_array($xValue)) {
                $result = self::array_flatten($xValue, $result);
            } else if (isset($xValue)) {
                $result[] = $xValue;
            }
        }

        return $result;
    }
}
