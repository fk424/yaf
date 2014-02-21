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

    static public function checkParams($params, $keys = null)
    {
        if (!is_null($keys))
        {
            foreach ($params as $k => $v)
            {
                if (!in_array($k, $keys))
                {
                    unset($params[$k]);
                }
            }
        }
        foreach ($params as $k => $v)
        {
            switch ($k) {
                case 'userId':
                    self::assert_int($v, EAPI_PARAM_USER_ID_INVALID);
                    break;
                case 'companyAddress':
                    self::assert_strlen($v, EAPI_PARAM_USER_COMPANY_ADDRESS_TOO_LONG, 120);
                    break;
                case 'contracter':
                    self::assert_strlen($v, EAPI_PARAM_USER_CONTRACTER_TOO_LONG, 8);
                    break;
                case 'email':
                    self::assert_strlen($v, EAPI_PARAM_USER_EMAIL_TOO_LONG, 50);
                    self::assert_regex($v, EAPI_PARAM_USER_EMAIL_INVALID_EMAIL, 'email');
                    break;
                case 'contracterPhone':
                    self::assert_strlen($v, EAPI_PARAM_USER_CONTRACTER_PHONE_INVALID_PHONE, 13);
                    self::assert_regex($v, EAPI_PARAM_USER_CONTRACTER_PHONE_INVALID_PHONE, 'phone');
                    break;
                case 'mobile':
                    self::assert_strlen($v, EAPI_PARAM_USER_MOBILE_INVALID_MOBILE, 13);
                    self::assert_regex($v, EAPI_PARAM_USER_MOBILE_INVALID_MOBILE, 'mobile');
                    break;
                case 'website':
                    self::assert_strlen($v, EAPI_PARAM_USER_WEBSITE_TOO_LONG, 255);
                    self::assert_regex($v, EAPI_PARAM_USER_WEBSITE_INVALID_URL, 'url');
                    break;
                case 'companyName':
                    self::assert_strlen($v, EAPI_PARAM_USER_COMPANY_NAME_TOO_LONG, 80);
                    break;
                case 'userIndustry':
                    self::assert_int($v, EAPI_PARAM_USER_USER_INDUSTRY_INVALID);
                    break;
                case 'areaId':
                    self::assert_int($v, EAPI_PARAM_USER_AREA_ID_INVALID);
                    break;
                case 'clientCategory':
                    self::assert_int($v, EAPI_PARAM_USER_CLIENT_CATEGORY_INVALID);
                    break;
                case 'userCategory':
                    self::assert_int($v, EAPI_PARAM_USER_USER_CATEGORY_INVALID);
                    break;
                case 'allowWebsite':
                    self::assert_strlen($v, EAPI_PARAM_USER_ALLOW_WEBSITE_TOO_LONG, 255);
                    break;
                default:
                    break;
            }

        }
        return $params;

    }
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