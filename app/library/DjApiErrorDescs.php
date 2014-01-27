<?php
define('DJAPI_EC_SUCCESS', 0);
define('DJAPI_EC_PARTIAL', 10001);
define('DJAPI_EC_FAIL', 10002);
define('DJ_DISABLED_CRM', 1);

/**
 * general errors
 */
define('DJAPI_EC_UNKNOWN', 90001);
define('DJAPI_EC_SERVICE', 90002);
define('DJAPI_EC_PERM_USER_DATA', 90003);
define('DJAPI_EC_PERM_REFERER', 90004);
define('DJAPI_EC_DATABASE', 90005);
define('DJAPI_EC_PARAM_CALL_ID', 90006);
define('DJAPI_EC_PARAM_SIGNATURE', 90007);
define('DJAPI_EC_PARAM_TOO_MANY', 90008);
define('DJAPI_EC_PARAM_SIGMETHOD', 90009);
define('DJAPI_EC_PARAM_USER_FIELD', 90010);
define('DJAPI_EC_PARAM_USER_ID', 90011);
define('DJAPI_EC_PARAM_TIMESTAMP', 90012);
define('DJAPI_EC_METHOD', 90101);
define('DJAPI_EC_PARAMETER_ERROR', 90201);
define('DJAPI_EC_CLIENT_IP', 90301);
define('DJAPI_EC_EXCEED_QUOTA', 90401);
define('DJAPI_EC_BAD_REQUEST_TYPE', 90601);

define('DJAPI_EC_CREATIVE_NOT_EXIST', 50101);
define('DJAPI_EC_CREATIVE_GROUP_NULL', 50201);
define('DJAPI_EC_CREATIVE_TITLE_NULL', 50202);
define('DJAPI_EC_CREATIVE_DESC_NULL', 50203);
define('DJAPI_EC_CREATIVE_DEST_URL_NULL', 50204);
define('DJAPI_EC_CREATIVE_ID_NULL', 50205);
define('DJAPI_EC_CREATIVE_IDLIST_NULL', 50206);
define('DJAPI_EC_CREATIVE_ID_INVALID', 50207);
define('DJAPI_EC_CREATIVE_EXCEED_ACTION', 50401);
define('DJAPI_EC_CREATIVE_DESC_EXCEED_LENGTH', 50402);
define('DJAPI_EC_CREATIVE_TITLE_EXCEED_LENGTH', 50403);
define('DJAPI_EC_CREATIVE_DEST_URL_EXCEED_LENGTH', 50404);
define('DJAPI_EC_CREATIVE_SHOW_URL_EXCEED_LENGTH', 50405);
define('DJAPI_EC_CREATIVE_TOO_MANY', 50406);
define('DJAPI_EC_CREATIVE_SHOW_URL_DOMAIN', 50601);
define('DJAPI_EC_CREATIVE_DEST_URL_DOMAIN', 50602);
define('DJAPI_EC_CREATIVE_DEST_URL_FILETYPE', 50603);
define('DJAPI_EC_CREATIVE_SHOW_URL_FORMAT', 50604);
define('DJAPI_EC_CREATIVE_DEST_URL_FORMAT', 50605);
define('DJAPI_EC_CREATIVE_INVALID_CHAR', 50606);
define('DJAPI_EC_CREATIVE_TRADEMARK', 50607);
define('DJAPI_EC_CREATIVE_COMPETE_WORD', 50608);
define('DJAPI_EC_CREATIVE_BLACKWORD', 50609);
define('DJAPI_EC_CREATIVE_STATUS', 50610);
define('DJAPI_EC_CREATIVE_DATETIME_SMALL', 50701);
define('DJAPI_EC_CREATIVE_DATETIME_BIG', 50702);
define('DJAPI_EC_CREATIVE_TITLE_WILDCARD', 50801);
define('DJAPI_EC_CREATIVE_EGOODS_ID_NOT_BELONG_USER', 50802);

define('DJAPI_EC_BUDGET_EMPTY', 20201);
define('DJAPI_EC_BUDGET_BELOW_MINI', 20501);
define('DJAPI_EC_EXCLUDE_IP_NULL', 20101);
define('DJAPI_EC_EXCLUDE_IP_MAX', 20102);

define('DJAPI_EC_ACCOUNT_NOT_EXIST', 20301);
define('DJAPI_EC_ACCOUNT_EXIST_COMPANY', 20302);
define('DJAPI_EC_ACCOUNT_EXIST_WEBSITE', 20303);
define('DJAPI_EC_ACCOUNT_CRM_ERROR', 20304);

define('DJAPI_EC_ACCOUNT_COMPANY_NULL', 20305);
define('DJAPI_EC_ACCOUNT_AREA_NULL', 20306);
define('DJAPI_EC_ACCOUNT_CONTACTER_NULL', 20307);
define('DJAPI_EC_ACCOUNT_EMAIL_NULL', 20308);
define('DJAPI_EC_ACCOUNT_WEBSITE_NULL', 20309);
define('DJAPI_EC_ACCOUNT_TEL_NULL', 20310);
define('DJAPI_EC_ACCOUNT_MOBILE_NULL', 20311);
define('DJAPI_EC_ACCOUNT_BUSINESSLICENSE_NULL', 20312);
define('DJAPI_EC_ACCOUNT_TYPE_NULL', 20313);
define('DJAPI_EC_ACCOUNT_EMAIL_ERROR', 20314);
define('DJAPI_EC_ACCOUNT_WEBSITE_ERROR', 20315);
define('DJAPI_EC_ACCOUNT_TEL_FORMAT', 20316);
define('DJAPI_EC_ACCOUNT_MOBILE_FORMAT', 20317);
define('DJAPI_EC_ACCOUNT_TYPE_ERROR', 20318);
define('DJAPI_EC_ACCOUNT_AREA_ERROR', 20319);
define('DJAPI_EC_ACCOUNT_ADDRESS_NULL', 20320);
define('DJAPI_EC_ACCOUNT_ASSIGN_ERROR', 20321);

define('DJAPI_EC_AREA_ID_NULL', 21000);
define('DJAPI_EC_AREA_INFO_NULL', 21001);
define('DJAPI_EC_AREA_PROVINCE_NULL', 21002);
define('DJAPI_EC_AREA_PROVINCE_ID_NULL', 21003);


define('DJAPI_EC_CAMPAIN_NOT_EXIST', 30101);
define('DJAPI_EC_CAMPAIN_NAME_NULL', 30201);
define('DJAPI_EC_CAMPAIN_ID_NULL', 30202);
define('DJAPI_EC_CAMPAIN_ID_INVALID', 30203);
define('DJAPI_EC_DUPLICATE_CAMPAIN_NAME', 30301);
define('DJAPI_EC_CAMPAIN_EXCEED_LENGTH', 30401);
define('DJAPI_EC_CAMPAIGNS_TOO_MANY', 30402);
define('DJAPI_EC_CAMPAIGN_BELOW_MINI', 30501);
define('DJAPI_EC_CAMPAIGN_EXCEED_MAX', 30502);
define('DJAPI_EC_CAMPAIGN_BUDGET_IS_VALID', 30503);
define('DJAPI_EC_CAMPAIGN_BUDGET_NULL', 30504);
define('DJAPI_EC_CAMPAIGN_STATUS', 30601);
define('DJAPI_EC_CAMPAIGN_REGION', 30602);
define('DJAPI_EC_CAMPAIGN_SCHEDULE_SETTING', 30603);
define('DJAPI_EC_CAMPAIGN_DATE_RANGE', 30604);
define('DJAPI_EC_CAMPAIGN_INVALID_DATE', 30605);
define('DJAPI_EC_CAMPAIGN_INVALID_TYPE', 30701);
define('DJAPI_EC_CAMPAIGN_TYPE_NULL', 30702);
define('DJAPI_EC_CAMPAIGN_UPDATE_TOO_MANY', 30801);
define('DJAPI_EC_CAMPAIGN_AREA_FAIL', 31001);
define('DJAPI_EC_CAMPAIGN_SERVICE_FAIL', 31002);
define('DJAPI_EC_CAMPAIGN_AREA_SERVICE_FAIL', 31003);

define('DJAPI_EC_GROUP_NOT_EXIST', 40101);
define('DJAPI_EC_GROUP_NAME_NULL', 40201);
define('DJAPI_EC_GROUP_PRICE_NULL', 40202);
define('DJAPI_EC_GROUP_ID_NULL', 40203);
define('DJAPI_EC_GROUP_ID_INVALID', 40204);
define('DJAPI_EC_DUPLICATE_GROUP_NAME', 40301);
define('DJAPI_EC_NEGATIVE_KEYWORD_CONFICTED', 40302);
define('DJAPI_EC_BIDDING_PRICE_MAX', 40401);
define('DJAPI_EC_GROUP_NAME_EXCEEDED', 40402);
define('DJAPI_EC_EXCEEDED_MAX_NEGATIVE_KEYWORD', 40403);
define('DJAPI_EC_TOO_MANY_GROUPS_IN_ONE', 40404);
define('DJAPI_EC_NEGATIVE_KEYWORD_EXCEEDED', 40405);
define('DJAPI_EC_NEGATIVE_KEYWORD_INVALID', 40406);
define('DJAPI_EC_GROUP_TYPE_NULL', 40407);
define('DJAPI_EC_PRICE_BELOW_MINI', 40501);
define('DJAPI_EC_GROUP_INVALID_STATUS', 40601);

define('DJAPI_EC_KEYWORD_NOT_EXIST', 60101);
define('DJAPI_EC_KEYWORD_NULL', 60102);
define('DJAPI_EC_KEYWORD_INVALID', 60103);
define('DJAPI_EC_KEYWORD_GROUP_NULL', 60201);
define('DJAPI_EC_KEYWORD_ID_NULL', 60202);
define('DJAPI_EC_KEYWORD_IDLIST_NULL', 60203);
define('DJAPI_EC_KEYWORD_ID_INVALID', 60204);
define('DJAPI_EC_KEYWORD_DUPLICATE', 60304);
define('DJAPI_EC_KEYWORD_DUPLICATE_DELETED', 60305);
define('DJAPI_EC_KEYWORD_EXCEED_ACTION', 60401);
define('DJAPI_EC_KEYWORD_EXCEED_RESULT', 60402);
define('DJAPI_EC_KEYWORD_PRICE_EXCEED_SYSTEM', 60403);
define('DJAPI_EC_KEYWORD_PRICE_EXCEED_QUOTA', 60404);
define('DJAPI_EC_KEYWORD_TOO_MANY', 60405);
define('DJAPI_EC_KEYWORD_EXCEED_LENGTH', 60406);
define('DJAPI_EC_KEYWORD_DEST_URL_EXCEED_LENGTH', 60407);
define('DJAPI_EC_KEYWORD_PRICE_LOW_SYSTEM', 60501);
define('DJAPI_EC_KEYWORD_PRICE_LOW_BID', 60502);
define('DJAPI_EC_KEYWORD_STATUS', 60601);
define('DJAPI_EC_KEYWORD_URL_DOMAIN', 60602);
define('DJAPI_EC_KEYWORD_URL_FILETYPE', 60603);
define('DJAPI_EC_KEYWORD_URL_FORMAT', 60604);
define('DJAPI_EC_KEYWORD_MATCH_TYPE', 60605);
define('DJAPI_EC_KEYWORD_INVALID_CHAR', 60606);
define('DJAPI_EC_KEYWORD_TRADEMARK', 60607);
define('DJAPI_EC_KEYWORD_COMPETE_WORD', 60608);
define('DJAPI_EC_KEYWORD_BLACKWORD', 60609);
define('DJAPI_EC_KEYWORD_COMPETE_WORD_ID_NULL', 60610);
define('DJAPI_EC_KEYWORD_COMPETE_WORD_ID_INVALID', 60611);
define('DJAPI_EC_KEYWORD_COMPETE_WORD_TOO_MANY', 60612);
define('DJAPI_EC_KEYWORD_COMPETE_WORD_NOT_EXIST', 60613);
define('DJAPI_EC_KEYWORD_MATCH_TYPE_IS_NULL', 60614);
define('DJAPI_EC_KEYWORD_STATUS_IS_NULL', 60615);
define('DJAPI_EC_KEYWORD_DATETIME_SMALL', 60701);
define('DJAPI_EC_KEYWORD_DATETIME_BIG', 60702);
define('DJAPI_EC_KEYWORD_DIFF_GROUP', 60801);
define('DJAPI_EC_KEYWORD_PARAM_WARNING', 60802);
define('DJAPI_EC_KEYWORD_PRICE_WARNING', 60803);
define('DJAPI_EC_KEYWORD_BATCH_NUM_INVALID', 60804);
define('DJAPI_EC_KEYWORD_PRICE_INVALID', 60805);
define('DJAPI_EC_KEYWORD_PRICE_IS_NULL', 60806);

define('DJAPI_EC_INTERFACE_WRONG_FORMAT', 100001);
define('DJAPI_EC_INTERFACE_PARAMS_INVALID', 100101);
define('DJAPI_EC_INTEREST_ID', 70101);

define('DJAPI_EC_CERT_DATA_ERROR', 60001);
define('DJAPI_EC_CERT_COUNT_OVERFLOW', 60002);
define('DJAPI_EC_CERT_DATA_EMPTY', 60003);
define('DJAPI_EC_CERT_EXPIRE_SHORT', 60004);

define('DJAPI_EC_EGOODS_PREMISSION_DENIED', 100201);
define('DJAPI_EC_EGOODS_GROUP_NOT_MATCH_TYPE', 100202);
define('DJAPI_EC_EGOODS_ID_INVALID', 100203);
define('DJAPI_EC_EGOODS_INFO_FAIL', 100204);
define('DJAPI_EC_EGOODS_OFFLINE', 100205);
define('DJAPI_EC_EGOODS_ID_IS_NULL', 100206);
define('DJAPI_EC_EGOODS_INFO_IS_NULL', 100207);
define('DJAPI_EC_EGOODS_INFO_INVALID', 100208);


define('DJAPI_EC_BATCH_IMPORT_ID',  160001);
define('DJAPI_EC_BATCH_OPER_TYPE',  160002);
define('DJAPI_EC_BATCH_EXCHANGE',   160003);

define('DJAPI_EC_BASE_GOOD_ID',   17001);

define('DJAPI_EC_ASSIST_APP_NULL', 70700);
define('DJAPI_EC_ASSIST_APP_OFFLINE', 70701);
define('DJAPI_EC_ASSIST_APP_USER_INFO', 70702);
define('DJAPI_EC_ASSIST_APP_PAGE_WARNING', 70703);

class DjApiErrorDescs
{

    public static $_arrSysOpenApiError = array(
        DJAPI_EC_SUCCESS => 'Ok',
        DJAPI_EC_PARTIAL => 'Partial success',
        
        DJAPI_EC_UNKNOWN => 'Unknown server error',
        DJAPI_EC_SERVICE => 'Service temporarily unavailable',
        DJAPI_EC_PERM_USER_DATA => 'No permission to access user data',
        DJAPI_EC_PERM_REFERER => 'No permission to access data for this referer',
        DJAPI_EC_DATABASE => 'DB error',
        DJAPI_EC_PARAM_CALL_ID => 'Invalid/Used call_id parameter',
        DJAPI_EC_PARAM_SIGNATURE => 'Incorrect signature',
        DJAPI_EC_PARAM_TOO_MANY => 'Too many parameters',
        DJAPI_EC_PARAM_SIGMETHOD => 'Unsupported signature method',
        DJAPI_EC_PARAM_USER_FIELD => 'Invalid user info field',
        DJAPI_EC_PARAM_USER_ID => 'Invalid user id',
        DJAPI_EC_PARAM_TIMESTAMP => 'Invalid/Used timestamp parameter',
        DJAPI_EC_METHOD => 'Unsupported openapi method',
        DJAPI_EC_CLIENT_IP => 'Unauthorized client IP address',
        DJAPI_EC_EXCEED_QUOTA => 'Not enough quota is available to process this command',
        DJAPI_EC_BAD_REQUEST_TYPE => 'Unsupported request type',
        
        DJAPI_EC_BUDGET_EMPTY => 'account budget is null',
        DJAPI_EC_BUDGET_BELOW_MINI => 'account Budget below system minimum setting',
        
        DJAPI_EC_CAMPAIN_NOT_EXIST => 'campaign not exist',
        DJAPI_EC_CAMPAIN_NAME_NULL => 'campaign name is null',
        DJAPI_EC_CAMPAIN_ID_NULL => 'campaign id is null',
        DJAPI_EC_CAMPAIN_ID_INVALID => 'campaign id is not for search',
        DJAPI_EC_DUPLICATE_CAMPAIN_NAME => 'Duplicate campaign Name',
        DJAPI_EC_CAMPAIGN_EXCEED_MAX => 'campaign Budget exceeded the limit',
        DJAPI_EC_CAMPAIN_EXCEED_LENGTH => 'campaign name exceeded the max length',
        DJAPI_EC_CAMPAIGNS_TOO_MANY => 'Too many campaigns in one account',
        DJAPI_EC_CAMPAIGN_BELOW_MINI => 'campaign Budget below system minimum setting',
        DJAPI_EC_CAMPAIGN_BUDGET_IS_VALID => 'campaign Budget is valid',
        DJAPI_EC_CAMPAIGN_BUDGET_NULL => 'campaign Budget is null',
        DJAPI_EC_CAMPAIGN_STATUS => 'Invalid value for campaign Status',
        DJAPI_EC_CAMPAIGN_REGION => 'Invalid region setting for geo-target',
        DJAPI_EC_CAMPAIGN_SCHEDULE_SETTING => 'Wrong format for schedule setting',
        DJAPI_EC_CAMPAIGN_DATE_RANGE => 'Wrong Start Day or End Day For Date Range',
        DJAPI_EC_CAMPAIGN_INVALID_DATE => 'Invalid start date or end date',
        DJAPI_EC_CAMPAIGN_INVALID_TYPE => 'Invalid type',
        DJAPI_EC_CAMPAIGN_TYPE_NULL => 'type is null',
        DJAPI_EC_CAMPAIGN_UPDATE_TOO_MANY => 'campaign Update too many',

        DJAPI_EC_CAMPAIGN_AREA_FAIL => 'area is fail',
        DJAPI_EC_CAMPAIGN_SERVICE_FAIL => 'service is fail',
        DJAPI_EC_CAMPAIGN_AREA_SERVICE_FAIL => 'area and service is fail',

        DJAPI_EC_GROUP_NOT_EXIST => 'group not exist',
        DJAPI_EC_GROUP_NAME_NULL => 'group name is null',
        DJAPI_EC_GROUP_PRICE_NULL => 'group default price is null',
        DJAPI_EC_GROUP_ID_NULL => 'group id is null',
        DJAPI_EC_GROUP_ID_INVALID => 'group id invalid',
        DJAPI_EC_DUPLICATE_GROUP_NAME => 'Duplicate Group Name',
        DJAPI_EC_NEGATIVE_KEYWORD_CONFICTED => 'Negative keyword conflicted with existing keyword',
        DJAPI_EC_BIDDING_PRICE_MAX => 'Bidding price exceeded the system max limit',
        DJAPI_EC_GROUP_NAME_EXCEEDED => 'Group name exceeded the max length',
        DJAPI_EC_EXCEEDED_MAX_NEGATIVE_KEYWORD => 'Exceeded max length of negative keyword',
        DJAPI_EC_TOO_MANY_GROUPS_IN_ONE => 'Too many Groups in one campaign',
        DJAPI_EC_NEGATIVE_KEYWORD_EXCEEDED => 'Negative Keyword numbers exceeded the limit',
        DJAPI_EC_NEGATIVE_KEYWORD_INVALID => 'Negative Keyword Included invalid character',
        DJAPI_EC_GROUP_TYPE_NULL => 'group type is null',
        DJAPI_EC_PRICE_BELOW_MINI => 'Bidding price below the system min limit',
        DJAPI_EC_GROUP_INVALID_STATUS => 'Invalid value for group Status',

        DJAPI_EC_CREATIVE_NOT_EXIST => 'creative not exist',
        DJAPI_EC_CREATIVE_GROUP_NULL => 'group id is null',
        DJAPI_EC_CREATIVE_TITLE_NULL => 'creative title is null',
        DJAPI_EC_CREATIVE_DESC_NULL => 'creative description is null',
        DJAPI_EC_CREATIVE_DEST_URL_NULL => 'creative destination url is null',
        DJAPI_EC_CREATIVE_ID_NULL => 'creative id is null',
        DJAPI_EC_CREATIVE_IDLIST_NULL => 'creative idlist is null',
        DJAPI_EC_CREATIVE_ID_INVALID => 'creative id is invalid',
        DJAPI_EC_CREATIVE_EXCEED_ACTION => 'Too Many actions in one request',
        DJAPI_EC_CREATIVE_DESC_EXCEED_LENGTH => 'The length of description exceeded the limit',
        DJAPI_EC_CREATIVE_TITLE_EXCEED_LENGTH => 'Title length exceeded the limit',
        DJAPI_EC_CREATIVE_DEST_URL_EXCEED_LENGTH => 'Destination url length exceeded the limit',
        DJAPI_EC_CREATIVE_SHOW_URL_EXCEED_LENGTH => 'Display url length exceeded the limit',
        DJAPI_EC_CREATIVE_TOO_MANY => 'Idea numbers exceeded the system limit',
        DJAPI_EC_CREATIVE_SHOW_URL_DOMAIN => 'The domain of display URL does not match the domain registered of this account',
        DJAPI_EC_CREATIVE_DEST_URL_DOMAIN => 'The domain of destination URL does not match the domain registered of this account',
        DJAPI_EC_CREATIVE_DEST_URL_FILETYPE => 'The file type of creative URL does not support',
        DJAPI_EC_CREATIVE_SHOW_URL_FORMAT => 'Illegal display url',
        DJAPI_EC_CREATIVE_DEST_URL_FORMAT => 'Illegal destination url',
        DJAPI_EC_CREATIVE_INVALID_CHAR => 'Included invalid character',
        DJAPI_EC_CREATIVE_TRADEMARK => 'Included trademark',
        DJAPI_EC_CREATIVE_COMPETE_WORD => 'Included competing word',
        DJAPI_EC_CREATIVE_BLACKWORD => 'Included blackword',
        DJAPI_EC_CREATIVE_STATUS => 'Invalid value for creative Status',
        DJAPI_EC_CREATIVE_DATETIME_SMALL => 'Datetime too early than now',
        DJAPI_EC_CREATIVE_DATETIME_BIG => 'Datetime too far from now',
        DJAPI_EC_CREATIVE_TITLE_WILDCARD => 'creative title can not include wildcard',
        DJAPI_EC_CREATIVE_EGOODS_ID_NOT_BELONG_USER => 'egoods id not belong this user',
        
        DJAPI_EC_KEYWORD_NOT_EXIST => 'keyword not exist',
        DJAPI_EC_KEYWORD_NULL => 'keyword is null',
        DJAPI_EC_KEYWORD_INVALID => 'keyword invalid',
        DJAPI_EC_KEYWORD_GROUP_NULL => 'group id is null',
        DJAPI_EC_KEYWORD_ID_NULL => 'keyword id is null',
        DJAPI_EC_KEYWORD_IDLIST_NULL => 'keyword idlist is null',
        DJAPI_EC_KEYWORD_ID_INVALID => 'keyword id invalid',
        DJAPI_EC_KEYWORD_DUPLICATE => 'Duplicate Keyword, The Keyword passed in already exists',
        DJAPI_EC_KEYWORD_EXCEED_ACTION => 'Too Many actions in one request',
        DJAPI_EC_KEYWORD_EXCEED_RESULT => 'Too Many results in one request',
        DJAPI_EC_KEYWORD_PRICE_EXCEED_SYSTEM => 'Bidding price exceeded the system max limit',
        DJAPI_EC_KEYWORD_PRICE_EXCEED_QUOTA => 'Bidding price exceeded the campaign daily budget or account daily budget',
        DJAPI_EC_KEYWORD_TOO_MANY => 'keyword numbers exceeded the system limit',
        DJAPI_EC_KEYWORD_EXCEED_LENGTH => 'Exceeded max length of keyword',
        DJAPI_EC_KEYWORD_DEST_URL_EXCEED_LENGTH => 'Destination url length exceeded the limit',
        DJAPI_EC_KEYWORD_PRICE_LOW_SYSTEM => 'Bidding price below the system min limit',
        DJAPI_EC_KEYWORD_PRICE_LOW_BID => 'Keyword Bid is below the minimum bid price',
        DJAPI_EC_KEYWORD_STATUS => 'Invalid value for keyword Status',
        DJAPI_EC_KEYWORD_STATUS_IS_NULL => 'Keyword Status is null',
        DJAPI_EC_KEYWORD_URL_DOMAIN => 'The domain of destination URL does not match the domain registered of this account',
        DJAPI_EC_KEYWORD_URL_FILETYPE => 'The file type of keyword URL does not support',
        DJAPI_EC_KEYWORD_URL_FORMAT => 'Illegal destination url',
        DJAPI_EC_KEYWORD_MATCH_TYPE => 'Word Match is illegal',
        DJAPI_EC_KEYWORD_MATCH_TYPE_IS_NULL => 'Word Match is null',
        DJAPI_EC_KEYWORD_INVALID_CHAR => 'Included invalid character',
        DJAPI_EC_KEYWORD_TRADEMARK => 'Included trademark',
        DJAPI_EC_KEYWORD_COMPETE_WORD => 'Included competing word',
        DJAPI_EC_KEYWORD_BLACKWORD => 'Included blackword',
        DJAPI_EC_KEYWORD_COMPETE_WORD_ID_NULL => 'Competing word id is null',
        DJAPI_EC_KEYWORD_COMPETE_WORD_ID_INVALID => 'Competing word id invalid',
        DJAPI_EC_KEYWORD_COMPETE_WORD_TOO_MANY => 'Competing word over max num',
        DJAPI_EC_KEYWORD_COMPETE_WORD_NOT_EXIST => 'Competing word not exist',
        DJAPI_EC_KEYWORD_DATETIME_SMALL => 'Datetime too early than now',
        DJAPI_EC_KEYWORD_DATETIME_BIG => 'Datetime too far from now',
        DJAPI_EC_KEYWORD_DIFF_GROUP => 'Words do not belong to the group',
        DJAPI_EC_KEYWORD_BATCH_NUM_INVALID => 'Nums of batch operate words is invalid',
        DJAPI_EC_KEYWORD_PRICE_INVALID => 'Price is invalid',
        DJAPI_EC_KEYWORD_PRICE_IS_NULL => 'Price is null',
        
        DJAPI_EC_PARAMETER_ERROR => 'Parameter Error',
        DJAPI_EC_EXCLUDE_IP_NULL => 'Exclude ip is null',
        DJAPI_EC_EXCLUDE_IP_MAX => 'Exclude ip exceeded max limit',
        80101 => 'Wrong ad account',
        80102 => 'Invalid access Token',
        80103 => 'Invalid api key',
        80401 => 'Exceeded the limit of requests',
        80402 => 'Too many requests in one minute',
        DJAPI_EC_INTEREST_ID => 'interest id is null',
        DJAPI_EC_INTERFACE_WRONG_FORMAT => 'wrong format',
        DJAPI_EC_INTERFACE_PARAMS_INVALID => 'params invalid',

        DJAPI_EC_EGOODS_PREMISSION_DENIED => 'egoods premission denied',
        DJAPI_EC_EGOODS_GROUP_NOT_MATCH_TYPE => 'not match egoods group type',
        DJAPI_EC_EGOODS_ID_INVALID => 'egood id invalid',
        DJAPI_EC_EGOODS_INFO_FAIL => 'get apith info fail',
        DJAPI_EC_EGOODS_OFFLINE => 'product has been off line',
        DJAPI_EC_EGOODS_ID_IS_NULL => 'egood id is null',
        DJAPI_EC_EGOODS_INFO_IS_NULL => 'goodsInfo is null',
        DJAPI_EC_EGOODS_INFO_INVALID => 'goodsInfo invalid',

        DJAPI_EC_BATCH_IMPORT_ID => 'empty import id',
        DJAPI_EC_BATCH_OPER_TYPE => 'empty oper_type',
        DJAPI_EC_BATCH_EXCHANGE => 'invalid exchange',

        DJAPI_EC_BASE_GOOD_ID => 'empty good id',
        DJAPI_EC_ASSIST_APP_USER_INFO => 'user id or user name',
        DJAPI_EC_ASSIST_APP_PAGE_WARNING => 'pn or rn is warning!',
    );

    public static function errmsg ($errcode)
    {
        if (isset(self::$_arrSysOpenApiError[$errcode])) {
            return self::$_arrSysOpenApiError[$errcode];
        } else {
            return self::$_arrSysOpenApiError[DJAPI_EC_SUCCESS];
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
