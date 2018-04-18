<?php

include_once("../utils/error.inc.php");

class Util
{

    /**
     * @param $var
     * @return bool
     */
    public static function notEmptyStr($var)
    {
        return is_string($var) && ($var != "");
    }

    /**
     * @param $var
     * @param $name
     * @throws ParameterError
     */
    public static function checkNotEmptyStr($var, $name)
    {
        if (!self::notEmptyStr($var)) {
            throw new ParameterError($name . " can not be empty string");
        }
    }

    /**
     * @param $var
     * @param $name
     * @throws ParameterError
     */
    public static function checkIsUInt($var, $name)
    {
        if (!(is_int($var) && $var >= 0)) {
            throw new ParameterError($name . " need unsigned int");
        }
    }

    /**
     * @param $var
     * @param $name
     * @throws ParameterError
     */
    public static function checkNotEmptyArray($var, $name)
    {
        if (!is_array($var) || count($var) == 0) {
            throw new ParameterError($name . " can not be empty array");
        }
    }

    /**
     * @param $var
     * @param $name
     * @param $args
     */
    public static function setIfNotNull($var, $name, &$args)
    {
        if (!is_null($var)) {
            $args[$name] = $var;
        }
    }

    /**
     * @param $array
     * @param $key
     * @param null $default
     * @return null
     */
    public static function arrayGet($array, $key, $default = null)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        return $default;
    }

    /**
     * 数组 转 对象
     * @param array $arr 数组
     * @return object|void
     */
    public function array2Object($arr)
    {
        if (gettype($arr) != 'array') {
            return;
        }
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object') {
                $arr[$k] = (object)self::Array2Object($v);
            }
        }

        return (object)$arr;
    }

    /**
     * 对象 转 数组
     *
     * @param object $object 对象
     * @return array|object
     */
    public function object2Array($object)
    {
        if (is_object($object) || is_array($object)) {
            $array = array();
            foreach ($object as $key => $value) {
                if ($value == null) {
                    continue;
                }
                $array[$key] = self::Object2Array($value);
            }
            return $array;
        } else {
            return $object;
        }
    }

    /**
     * 数组转XML
     * @param $rootName
     * @param $arr
     * @return string
     */
    public function array2Xml($rootName, $arr)
    {
        $xml = "<".$rootName.">";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml.="<".$key.">".$val."</".$key.">";
            } else {
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</".$rootName.">";
        return $xml;
    }

    /**
     * 将XML转为array
     * @param $xml
     * @return mixed
     */
    public function xml2Array($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }
} // class Utils
