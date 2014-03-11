<?php

/*
 * exception方式只应该用于controller，否则将破坏结构性。
 */

class Eapi_Checker
{
	static $regex_arr = array(
		'email' => '/^[\w\d]+[\w\d-.]*@[\w\d-.]+\.[\w\d]{2,10}$/i',
		'url' => '/^(http:\/\/)?(https:\/\/)?([\w\d-]+\.)+[\w-]+(\/[\d\w-.\/?%&=]*)?$/',
		'phone' => '/^0[0-9]{2,3}[-]?\d{7,8}$/',
		'mobile' => '/^[(86)|0]?(13\d{9})|(15\d{9})|(18\d{9})$/',
		);

	public static function getParam($key, $exception_code = 0, $default = 0)
	{
        $params = Yaf_Registry::get("params");
		$empty = false;
		if (!isset($params[$key])) {
			$empty = true;
		} else {
			$v = $params[$key];
			if (empty($v)) {
				$empty = true;
			}
		}
		if ($empty) {
			if ($exception_code > 0) {
				throw new Eapi_Exception($exception_code);
			} else {
				$v = $default;
			}
		}
		return $v;
	}

	public static function assert_trans($old, $new, $trans, $exception_code = 0) {
		if (isset($trans[$old]) && is_array($trans[$old]) && in_array($new, $trans))
		{
			return true;
		} else {
			throw new Eapi_Exception($exception_code);
		}
	}

	public static function assert_enum($v, $exception_code = 0, $enum_arr)
	{
        $bool = (!is_int($v) ? (ctype_digit($v)) : true);
        if ($bool) {
        	if (!in_array($v, $enum_arr)){
				throw new Eapi_Exception($exception_code);
        	}
        } else {
        	if (!array_key_exists($v, $enum_arr)) {
				throw new Eapi_Exception($exception_code);
        	} else {
        		$v = $enum_arr[$v];
        	}
        }
        return $v;
	}


	public static function assert_int($v, $exception_code = 0)
	{
        $bool = (!is_int($v) ? (ctype_digit($v)) : true);
        if (!$bool) {
        	if ($exception_code > 0) {
				throw new Eapi_Exception($exception_code);
        	} else {
        		$v = 0;
        	}
        }
        return $v;
	}

    public static function assert_empty_array($v, $exception_code)
    {
        if (!is_array($v) || (count($v) == 0)){
            throw new Eapi_Exception($exception_code);
        }
        return $v;
    }

	public static function assert_json($v, $exception_code)
	{
		$vv = json_decode($v, true);
		if (!is_array($vv)){
			throw new Eapi_Exception($exception_code);
		}
		return $vv;
	}

	public static function assert_strlen($v, $exception_code, $len)
	{
		if (strlen($v)>$len) {
			throw new Eapi_Exception($exception_code);
		}
		return $v;
	}

	public static function assert_regex($v, $exception_code, $type)
	{
		$match = self::$regex_arr[$type];
        $v = trim($v);
        if (!preg_match($match,$v)) {
			throw new Eapi_Exception($exception_code);
        }
        return $v;
	}


}