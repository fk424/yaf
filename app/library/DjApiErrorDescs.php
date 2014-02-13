<?php
define('EAPI_SUCCESS', 0);
//Param
define('EAPI_PARAM_USER_ID_NULL', 10101);
define('EAPI_PARAM_USER_ID_INVALID', 10102);

//Account
define('EAPI_USER_NOT_EXIST', 20301);


class DjApiErrorDescs
{

    public static $_arrSysOpenApiError = array(
        EAPI_PARAM_USER_ID_NULL => 'Param userId is null',
        EAPI_PARAM_USER_ID_INVALID => 'Param userId is invalid',
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
