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
