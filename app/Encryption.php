<?php

namespace Aums;

class Encryption
{
    private static $skey = 'kqrktDyzrL1TZPv22WMpbaNM5ONFveGZ'; // you can change it

    public static function safe_b64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(['+', '/', '='], ['-', '_', ''], $data);

        return $data;
    }

    public static function safe_b64decode($string)
    {
        $data = str_replace(['-', '_'], ['+', '/'], $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }

        return base64_decode($data);
    }

    public static function encode($value, $key = null)
    {
        if ($key == null) {
            $key = self::$skey;
        } else {
            $key = pack('H*', $key);
        }
        if (!$value) {
            return false;
        }
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);

        return trim(self::safe_b64encode($crypttext));
    }

    public static function decode($value, $key = null)
    {
        if ($key == null) {
            $key = self::$skey;
        } else {
            $key = pack('H*', $key);
        }
        if (!$value) {
            return false;
        }
        $crypttext = self::safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $crypttext, MCRYPT_MODE_ECB, $iv);

        return trim($decrypttext);
    }
}
