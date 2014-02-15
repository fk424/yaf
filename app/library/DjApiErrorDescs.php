<?php
define('EAPI_SUCCESS', 0);
//Param
define('EAPI_PARAM_NULL', 10001);
//user
define('EAPI_PARAM_USER_ID_NULL', 11001);
define('EAPI_PARAM_USER_ID_INVALID', 11002);
define('EAPI_PARAM_USER_COMPANY_ADDRESS_TOO_LONG', 11013);
define('EAPI_PARAM_USER_EMAIL_TOO_LONG', 11023);
define('EAPI_PARAM_USER_EMAIL_INVALID_EMAIL', 11026);
define('EAPI_PARAM_USER_MOBILE_INVALID_MOBILE', 11036);
define('EAPI_PARAM_USER_CONTRACTER_TOO_LONG', 11043);
define('EAPI_PARAM_USER_CONTRACTER_PHONE_INVALID_PHONE', 11056);
define('EAPI_PARAM_USER_WEBSITE_TOO_LONG', 11063);
define('EAPI_PARAM_USER_WEBSITE_INVALID_URL', 11066);
define('EAPI_PARAM_USER_COMPANY_NAME_TOO_LONG', 11073);
define('EAPI_PARAM_USER_USER_INDUSTRY_INVALID', 11082);
define('EAPI_PARAM_USER_AREA_ID_INVALID', 11092);
define('EAPI_PARAM_USER_CLIENT_CATEGORY_INVALID', 11102);
define('EAPI_PARAM_USER_USER_CATEGORY_INVALID', 11112);
define('EAPI_PARAM_USER_ALLOW_WEBSITE_TOO_LONG', 11123);


//Account
define('EAPI_USER_NOT_EXIST', 20301);


class DjApiErrorDescs
{

    public static $_arrSysOpenApiError = array(
        EAPI_PARAM_USER_ID_NULL => 'Param userId is null',
        EAPI_PARAM_USER_ID_INVALID => 'Param userId is invalid',
        EAPI_PARAM_USER_COMPANY_ADDRESS_TOO_LONG => 'Param companyAddress is too long',
        EAPI_PARAM_USER_EMAIL_TOO_LONG => 'Param email is too long',
        EAPI_PARAM_USER_EMAIL_INVALID_EMAIL => 'Param email is not a valid email',
        EAPI_PARAM_USER_MOBILE_INVALID_MOBILE => 'Param mobile is not a valid mobile',
        EAPI_PARAM_USER_CONTRACTER_TOO_LONG => 'Param contracter is too long',
        EAPI_PARAM_USER_CONTRACTER_PHONE_INVALID_PHONE => 'Param contracterPhone is not a valid phone',

        EAPI_PARAM_USER_WEBSITE_TOO_LONG => 'Param website is too long',
        EAPI_PARAM_USER_WEBSITE_INVALID_URL => 'Param website is invalid',
        EAPI_PARAM_USER_COMPANY_NAME_TOO_LONG => 'Param companyName is too long',
        EAPI_PARAM_USER_USER_INDUSTRY_INVALID => 'Param userIndustry is invalid',
        EAPI_PARAM_USER_AREA_ID_INVALID => 'Param areaId is invalid',
        EAPI_PARAM_USER_CLIENT_CATEGORY_INVALID => 'Param clientCategory is invalid',
        EAPI_PARAM_USER_USER_CATEGORY_INVALID => 'Param userCategory is invalid',
        EAPI_PARAM_USER_ALLOW_WEBSITE_TOO_LONG => 'Param allowWebsite is too long',

        EAPI_USER_NOT_EXIST => 'User not exist',
    );

    public static function errmsg ($errcode)
    {
        if (isset(self::$_arrSysOpenApiError[$errcode])) {
            return self::$_arrSysOpenApiError[$errcode];
        } else {
            return self::$_arrSysOpenApiError[EAPI_SUCCESS];
        }
    }

    public static function register ($arrErrDescs)
    {
        self::$_arrSysOpenApiError = self::$_arrSysOpenApiError + $arrErrDescs;
    }
}

class DjApiException extends Exception
{

    protected $_strDesc;

    public function __construct ($errcode, $desc = null, $errmsg = null)
    {
        $this->_strDesc = $desc;
        if (empty($errmsg)) {
            $errmsg = DjApiErrorDescs::errmsg($errcode);
        }
        parent::__construct($errmsg, $errcode);
    }

    public function getDesc ()
    {
        return $this->_strDesc;
    }
}
