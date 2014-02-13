<?php
class Checker
{
	public static function assert_empty($v, $exception_code = 0, $default = 0)
	{
		$empty = false;
		if (!isset($v)) {
			$empty = true;
		} elseif (empty($v)) {
			$empty = true;
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

	public static function assert_str($v, $assert = false)
	{

	}
}