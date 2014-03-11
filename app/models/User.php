<?php

class UserModel extends Eapi_ModelBase
{
    protected $_ttl = 600;
    protected $_cache_func = array(
        'getUserInfo' => 'user:detail',
        'getChannel' => 'user:channel',
        );
    protected $_decache_func = array(
        'updateDetail' => array('user:detail'),
        );

    static protected $_instance;

    const PRE_AUDIT = 0;     //未审核
    const NO_INFO = -1;      //未填写信息
    const POST_AUDIT = 1;    //已审核
    const NO_BALANCE = 2;    //余额不足
    const REJECTED = -3;     //审核被拒绝
    const IS_CANCELED = -4;  //账户关闭
    const PRE_DISPATCH = -5; //待分配审核
    const IS_DELETED = -6;   //被删除
    const PRE_CONFIRM = -7;  //待销售确认

    static public $status = array(
        'pre-audit' => self::PRE_AUDIT,
        'no-info' => self::NO_INFO, 
        'post-audit' => self::POST_AUDIT,
        'no-balance' => self::NO_BALANCE, 
        'rejected' => self::REJECTED,
        'cancled' => self::IS_CANCELED,
        'pre-dispatch' => self::PRE_DISPATCH,
        'deleted' => self::IS_DELETED,
        'pre-confirm' => self::PRE_CONFIRM
    );

    static public $status_trans = array(
        self::IS_DELETED => array(),
        self::NO_INFO => array(self::PRE_CONFIRM),
        self::PRE_CONFIRM => array(self::POST_AUDIT),
        self::POST_AUDIT => array(self::PRE_AUDIT),
        self::PRE_AUDIT => array(self::POST_AUDIT, self::REJECTED, self::IS_CANCELED),
        self::REJECTED => array(self::PRE_AUDIT),
    );

    const SEARCH_TEXT = 1;
    const SEARCH_GOODS = 6;
    const SHOW_TEXT = 2;
    const SHOW_IMG = 3;
    const SHOW_GOODS = 4;
    const SHOW_TUAN = 5;
    const ASSIST_SEARCH = 7;

    static public $search_type = array(self::SEARCH_TEXT, self::SEARCH_GOODS);
    static public $show_type = array(self::SHOW_TEXT, self::SHOW_IMG, self::SHOW_GOODS, self::SHOW_TUAN);
    static public $assist_type = array(self::ASSIST_SEARCH);


    static public function checkParams($params, $keys = null)
    {
        $params = parent::checkParams($params, $keys);
        foreach ($params as $k => $v)
        {
            switch ($k) {
                case 'companyAddress':
                    Eapi_Checker::assert_strlen($v, EAPI_PARAM_USER_COMPANY_ADDRESS_TOO_LONG, 120);
                    break;
                case 'contracter':
                    Eapi_Checker::assert_strlen($v, EAPI_PARAM_USER_CONTRACTER_TOO_LONG, 8);
                    break;
                case 'email':
                    Eapi_Checker::assert_strlen($v, EAPI_PARAM_USER_EMAIL_TOO_LONG, 50);
                    Eapi_Checker::assert_regex($v, EAPI_PARAM_USER_EMAIL_INVALID_EMAIL, 'email');
                    break;
                case 'contracterPhone':
                    Eapi_Checker::assert_strlen($v, EAPI_PARAM_USER_CONTRACTER_PHONE_INVALID_PHONE, 13);
                    Eapi_Checker::assert_regex($v, EAPI_PARAM_USER_CONTRACTER_PHONE_INVALID_PHONE, 'phone');
                    break;
                case 'mobile':
                    Eapi_Checker::assert_strlen($v, EAPI_PARAM_USER_MOBILE_INVALID_MOBILE, 13);
                    Eapi_Checker::assert_regex($v, EAPI_PARAM_USER_MOBILE_INVALID_MOBILE, 'mobile');
                    break;
                case 'website':
                    Eapi_Checker::assert_strlen($v, EAPI_PARAM_USER_WEBSITE_TOO_LONG, 255);
                    Eapi_Checker::assert_regex($v, EAPI_PARAM_USER_WEBSITE_INVALID_URL, 'url');
                    break;
                case 'companyName':
                    Eapi_Checker::assert_strlen($v, EAPI_PARAM_USER_COMPANY_NAME_TOO_LONG, 80);
                    break;
                case 'userIndustry':
                    Eapi_Checker::assert_int($v, EAPI_PARAM_USER_USER_INDUSTRY_INVALID);
                    break;
                case 'areaId':
                    Eapi_Checker::assert_int($v, EAPI_PARAM_USER_AREA_ID_INVALID);
                    break;
                case 'clientCategory':
                    Eapi_Checker::assert_int($v, EAPI_PARAM_USER_CLIENT_CATEGORY_INVALID);
                    break;
                case 'userCategory':
                    Eapi_Checker::assert_int($v, EAPI_PARAM_USER_USER_CATEGORY_INVALID);
                    break;
                case 'allowWebsite':
                    Eapi_Checker::assert_strlen($v, EAPI_PARAM_USER_ALLOW_WEBSITE_TOO_LONG, 255);
                    break;
                case 'status':
                    Eapi_Checker::assert_enum($v, EAPI_PARAM_USER_STATUS_INVALID, self::$status);
                    break;
                default:
                    break;
            }

        }
        return $params;

    }
    public function checkStatus($userId, $self_update = false)
    {
        $info = $this->getUserInfo_cache($userId);
        switch ($info['status']) {
            case -6:
                throw new Eapi_Exception(EAPI_USER_IS_DELETED);
                break;
            case -4:
                throw new Eapi_Exception(EAPI_USER_IS_CANCELED);
                break;
            case -1:
                if (!$self_update) {
                    throw new Eapi_Exception(EAPI_USER_HAS_NO_INFO);
                }
                break;
            default:
                break;
        }
    }

    public function getUserInfo($uid)
    {
        $postData = array(
            'userID' => $uid
        );

        $res = Utility::edcApiPost('eapi/SearchUser', $postData);
        if ($res['errno'] != 0) {
            throw new Eapi_Exception(80101);
        }
        $userInfo = $res['msg']['data'];

        if (!$userInfo) {
            throw new Eapi_Exception(EAPI_USER_NOT_EXIST);
        }
        return $userInfo;
    }

    public function getChannel($uid)
    {
        $channel = array();
        $edcData = array('userID' => $uid);
        $res = Utility::edcApiPost('user/searchExt', $edcData);
        if ($res['errno'] == 0) {
            if ($res['msg']['channels'] && is_array($res['msg']['channels'])) {
                foreach ($res['msg']['channels'] as $row) {
                    if (in_array($row['ctype'], array(self::SEARCH_TEXT, self::SEARCH_GOODS, self::SHOW_TEXT, self::SHOW_IMG, self::SHOW_GOODS, self::SHOW_TUAN, self::ASSIST_SEARCH))) {
                        $channel[] = $row['ctype'];
                    }
                }
            } else {
                $this->getUserInfo($uid);
                $channel[] = 1;
            }
        }

        $channel = array_unique($channel);

        return $channel;
    }

    public function getChannleType($uid)
    {
        $channel = $this->getChannel($uid);

        foreach ($this->search_type as $type) {
            if (in_array($type, $channel)) {
                return 1;
            }
        }

        foreach ($this->show_type as $type) {
            if (in_array($type, $channel)) {
                return 2;
            }
        }

        foreach ($this->assist_type as $type) {
            if (in_array($type, $channel)) {
                return 7;
            }
        }
    }

    public function updateDetail($intUserId, $data)
    {
        $data['user_id'] = $intUserId;
        $res = Utility::edcApiPost('edc/user/updatebyid', $data);

        if ($res['errno'] == 0) {
            return 0;
        }
        throw new DjApiException(DJAPI_EC_DATABASE);
    }


}

?>
