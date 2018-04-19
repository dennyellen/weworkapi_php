<?php
namespace WeWork\Util;

//include_once("../utils/error.inc.php");

use WeWork\Exceptions\HttpException;
use WeWork\Exceptions\InternalException;
use WeWork\Exceptions\NetWorkException;

class HttpUtil
{
    /**
     * @param $queryArgs
     * @return string
     */
    public static function makeUrl($queryArgs)
    {
        $base = "https://qyapi.weixin.qq.com";
        if (substr($queryArgs, 0, 1) === "/") {
            return $base . $queryArgs;
        }
        return $base . "/" . $queryArgs;
    }

    /**
     * @param $arr
     * @return string
     */
    public static function array2Json($arr)
    {
        $parts = array();
        $is_list = false;
        $keys = array_keys($arr);
        $max_length = count($arr) - 1;
        if (($keys [0] === 0) && ($keys [$max_length] === $max_length)) {
            $is_list = true;
            for ($i = 0; $i < count($keys); $i++) {
                if ($i != $keys [$i]) {
                    $is_list = false;
                    break;
                }
            }
        }
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                if ($is_list) {
                    $parts [] = self::array2Json($value);
                } else {
                    $parts [] = '"' . $key . '":' . self::array2Json($value);
                }
            } else {
                $str = '';
                if (!$is_list) {
                    $str = '"' . $key . '":';
                }
                if (!is_string($value) && is_numeric($value) && $value < 2000000000) {
                    $str .= $value;
                } elseif ($value === false) {
                    $str .= 'false';
                } elseif ($value === true) {
                    $str .= 'true';
                } else {
                    $str .= '"' . addcslashes($value, "\\\"\n\r\t/") . '"';
                }
                $parts[] = $str;
            }
        }
        $json = implode(',', $parts);
        if ($is_list) {
            return '[' . $json . ']';
        }
        return '{' . $json . '}';
    }

    /**
     * http get
     * @param string $url
     * @return mixed http response body
     * @throws InternalException
     */
    public static function httpGet($url)
    {
        $config = require(__DIR__ . './../config.php');
        if (true == $config['DEBUG']) {
            echo $url . "\n";
        }

        self::__checkDeps();
        $ch = curl_init();

        self::__setSSLOpts($ch, $url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        try {
            return self::__exec($ch);
        } catch (HttpException $e) {
        } catch (NetWorkException $e) {
        }
    }

    /**
     * http post
     * @param string $url
     * @param $postData
     * @return mixed http response body
     * @throws InternalException
     */
    public static function httpPost($url, $postData)
    {
        $config = require(__DIR__ . './../config.php');
        if (true == $config['DEBUG']) {
            echo $url . " -d $postData\n";
        }

        self::__checkDeps();
        $ch = curl_init();

        self::__setSSLOpts($ch, $url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);


        try {
            return self::__exec($ch);
        } catch (HttpException $e) {
        } catch (NetWorkException $e) {
        }
    }

    /**
     * @param $ch
     * @param $url
     */
    private static function __setSSLOpts($ch, $url)
    {
        if (stripos($url, "https://") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        }
    }

    /**
     * @param $ch
     * @return mixed
     * @throws HttpException
     * @throws NetWorkException
     */
    private static function __exec($ch)
    {
        $output = curl_exec($ch);
        $status = curl_getinfo($ch);
        curl_close($ch);

        if ($output === false) {
            throw new NetWorkException("network error");
        }

        if (intval($status["http_code"]) != 200) {
            throw new HttpException(
                "unexpected http code " . intval($status["http_code"])
            );
        }

        return $output;
    }

    /**
     * @throws InternalException
     */
    private static function __checkDeps()
    {
        if (!function_exists("curl_init")) {
            throw new InternalException("missing curl extend");
        }
    }
}
