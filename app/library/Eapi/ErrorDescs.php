<?php


class Eapi_ErrorDescs
{
    public static $_arrSysOpenApiError = array(
        EAPI_SUCCESS => 'ok',

        EAPI_OPTIONAL_PARAM_NULL => 'Optional Param is null',
        EAPI_PARAM_SPLIT_ID_NULL => 'Param splitId is null',
        EAPI_PARAM_SPLIT_ID_INVALID => 'Param splitId is invalid',

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

        EAPI_PARAM_USER_STATUS_NULL => 'Param status is null',
        EAPI_PARAM_USER_STATUS_INVALID => 'Param status is invalid',

        EAPI_PARAM_PLAN_ID_NULL => 'Param planId is null',
        EAPI_PARAM_PLAN_ID_INVALID => 'Param planId is invalid',
        EAPI_PARAM_PLAN_IDS_NULL => 'Param planIds is null',
        EAPI_PARAM_PLAN_IDS_INVALID => 'Param planIds is invalid',

        EAPI_USER_NOT_EXIST => 'User not exist',
        EAPI_USER_IS_DELETED => 'User is deleted',
        EAPI_USER_IS_CANCELED => 'User is canceled',
        EAPI_USER_HAS_NO_INFO => 'User has no info',

        EAPI_PLAN_NOT_EXIST => 'Plan not exist',
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
