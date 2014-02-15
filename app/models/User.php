<?php

class UserModel extends ModelDj
{
    protected $_cache_map = array(
        'getUserInfo'=> 'detail',
        'updateDetail'=> 'detail',
        'getChannel'=> 'channel',
        );
    protected $_cache_func = array(
        'getUserInfo_cache',
        'getChannel_cache',
        );
    protected $_decache_func = array(
        'updateDetail_decache',
        );
    protected $_key_prefix = 'user';

    static protected $_model;

    const SEARCH_TEXT = 1;
    const SEARCH_GOODS = 6;
    const SHOW_TEXT = 2;
    const SHOW_IMG = 3;
    const SHOW_GOODS = 4;
    const SHOW_TUAN = 5;
    const ASSIST_SEARCH = 7;

    public $search_type = array(self::SEARCH_TEXT, self::SEARCH_GOODS);
    public $show_type = array(self::SHOW_TEXT, self::SHOW_IMG, self::SHOW_GOODS, self::SHOW_TUAN);
    public $assist_type = array(self::ASSIST_SEARCH);

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
                    Checker::assert_int($v, EAPI_PARAM_USER_ID_INVALID);
                    break;
                case 'companyAddress':
                    Checker::assert_strlen($v, EAPI_PARAM_USER_COMPANY_ADDRESS_TOO_LONG, 120);
                    break;
                case 'contracter':
                    Checker::assert_strlen($v, EAPI_PARAM_USER_CONTRACTER_TOO_LONG, 8);
                    break;
                case 'email':
                    Checker::assert_strlen($v, EAPI_PARAM_USER_EMAIL_TOO_LONG, 50);
                    Checker::assert_regex($v, EAPI_PARAM_USER_EMAIL_INVALID_EMAIL, 'email');
                    break;
                case 'contracterPhone':
                    Checker::assert_strlen($v, EAPI_PARAM_USER_CONTRACTER_PHONE_INVALID_PHONE, 13);
                    Checker::assert_regex($v, EAPI_PARAM_USER_CONTRACTER_PHONE_INVALID_PHONE, 'phone');
                    break;
                case 'mobile':
                    Checker::assert_strlen($v, EAPI_PARAM_USER_MOBILE_INVALID_MOBILE, 13);
                    Checker::assert_regex($v, EAPI_PARAM_USER_MOBILE_INVALID_MOBILE, 'mobile');
                    break;
                case 'website':
                    Checker::assert_strlen($v, EAPI_PARAM_USER_WEBSITE_TOO_LONG, 255);
                    Checker::assert_regex($v, EAPI_PARAM_USER_WEBSITE_INVALID_URL, 'url');
                    break;
                case 'companyName':
                    Checker::assert_strlen($v, EAPI_PARAM_USER_COMPANY_NAME_TOO_LONG, 80);
                    break;
                case 'userIndustry':
                    Checker::assert_int($v, EAPI_PARAM_USER_USER_INDUSTRY_INVALID);
                    break;
                case 'areaId':
                    Checker::assert_int($v, EAPI_PARAM_USER_AREA_ID_INVALID);
                    break;
                case 'clientCategory':
                    Checker::assert_int($v, EAPI_PARAM_USER_CLIENT_CATEGORY_INVALID);
                    break;
                case 'userCategory':
                    Checker::assert_int($v, EAPI_PARAM_USER_USER_CATEGORY_INVALID);
                    break;
                case 'allowWebsite':
                    Checker::assert_strlen($v, EAPI_PARAM_USER_ALLOW_WEBSITE_TOO_LONG, 255);
                    break;
                default:
                    break;
            }

        }
        return $params;

    }

    public function getUserInfo($uid)
    {
        $postData = array(
            'userID' => $uid
        );

        $res = Utility::edcApiPost('eapi/SearchUser', $postData);
        if ($res['errno'] != 0) {
            throw new DjApiException(80101);
        }
        $userInfo = $res['msg']['data'];

        if (!$userInfo) {
            throw new DjApiException(EAPI_USER_NOT_EXIST);
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
