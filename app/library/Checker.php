<?php
class Checker
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
				throw new DjApiException($exception_code);
			} else {
				$v = $default;
			}
		}
		return $v;
	}

	public static function assert_int($v, $exception_code = 0)
	{
        $bool = (!is_int($v) ? (ctype_digit($v)) : true);
        if (!$bool) {
        	if ($exception_code > 0) {
				throw new DjApiException($exception_code);
        	} else {
        		$v = 0;
        	}
        }
        return $v;
	}

	public static function assert_strlen($v, $exception_code, $len)
	{
		if (strlen($v)>$len) {
			throw new DjApiException($exception_code);
		}
		return $v;

	}
	public static function assert_regex($v, $exception_code, $type)
	{
		$match = self::$regex_arr[$type];
        $v = trim($v);
        if (!preg_match($match,$v)) {
			throw new DjApiException($exception_code);
        }
        return $v;
	}


}