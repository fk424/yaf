<?php

class UserModel extends ModelDj
{
    protected $_cache_map = array(
        'getUserInfo'=> 'detail',
        'getChannel'=> 'channel',
        );
    protected $_cache_func = array(
        'getUserInfo_cache',
        'getChannel_cache',
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
            $redis = Yii::app()->openApiMaster;
            $key = 'eapi_user_detail_' . $intUserId;
            $redis->delete($key);

            return array(
                'errno' => DJAPI_EC_SUCCESS,
                'data' => array(
                    'affectedRecords' => 1
                )
            );
        }
        throw new DjApiException(DJAPI_EC_DATABASE);
    }


}

?>
