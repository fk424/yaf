<?php
/**
 * 默认的控制器
 * 当然, 默认的控制器, 动作, 模块都是可用通过配置修改的
 * 也可以通过$dispater->setDefault*Name来修改
 */
class UserController extends Yaf_Controller_Abstract {

	public function getChannelTypeAction() {
        $params = Yaf_Registry::get("params");
        $userId = $params['userId'];
        $userId = Checker::assert_empty($userId, EAPI_PARAM_USER_ID_NULL);
        $userId = Checker::assert_int($userId, EAPI_PARAM_USER_ID_INVALID);

//        $model = new UserModel();
        $model = UserModel::getInstance();

        $channel = $model->getChannel_cache($userId);
        if ($channel) {
            $channel = implode('|', $channel);
        }

        $this->getView()->assign("errno", EAPI_SUCCESS);
        $this->getView()->assign("data", $channel);
	}

    /**
     * 返回账户详细信息
     */
    public function getDetailAction ()
    {
        $params = Yaf_Registry::get("params");

        $userId = isset($params['userId']) ? $params['userId'] : 0;
        $userId = Checker::assert_empty($userId, EAPI_PARAM_USER_ID_NULL);
        $userId = Checker::assert_int($userId, EAPI_PARAM_USER_ID_INVALID);

        $model = UserModel::getInstance();

        $userInfo = $model->getUserInfo_cache($userId);
        $website = trim(str_replace(";", ",", $userInfo["website"]), ",");
        $allowwebsite = trim(str_replace(";", ",", $userInfo["allowwebsite"]), ",");
        $allowDomain = implode(",", array_filter(array(
            $website,
            $allowwebsite
        )));
        $userInfo['allowDomain'] = $allowDomain;

        $this->getView()->assign("errno", EAPI_SUCCESS);
        $this->getView()->assign("data", $userInfo);

    }

    public function updateUserDetailAction()
    {
        $params = Yaf_Registry::get("params");
        $userId = isset($params['userId']) ? $params['userId'] : 0;
        $userId = Checker::assert_empty($userId, EAPI_PARAM_USER_ID_NULL);
        $userId = Checker::assert_int($userId, EAPI_PARAM_USER_ID_INVALID);


        $postData = array(
            'userID' => $userId
        );

        $post = $params;

        $model = UserModel::getInstance();
var_dump($model);die;
        $model->updateDetail($userId, $post);

        if ($post['user_channel']) {
            $this->_updateUserChannel($intUserId, $post['user_channel']);
        }

        /*
        $mqData = $post;
        $mqData['ad_user_id'] = $intUserId;
        $mqData['logid'] = $logid;
        $mqData['ip_address'] = isset($_SERVER['HTTP_CLIENTIP']) ? $_SERVER['HTTP_CLIENTIP'] : '';
        $mqData['opt_user_id'] = isset($_SERVER['HTTP_OPTUSERID']) ? $_SERVER['HTTP_OPTUSERID'] : 0;

        CEmqPublisher::send(
            Yii::app()->params['exchange']['updateUserEapi'],
            __CLASS__ . '_' . __FUNCTION__,
            json_encode($mqData),
            $logid,
            Yii::app()->params['emq']
        );
         */

        return array(
            'errno' => DJAPI_EC_SUCCESS,
            'data' => 'ok'
        );
    }

    public function updateUserAction()
    {
        $objReqParam = DjApiRequestParam::instance();
        $intUserId = $objReqParam->getUserId();
        $params = $objReqParam->getRequest();
        $logid = $objReqParam->getParam('logid', '');

        $postData = array(
            'userID' => $intUserId
        );

        $userData = $this->user_model->getUserInfo($intUserId);

        $post = $params;

        //1 已审核 0 未审核账户 -1未填写信息 2余额不足 -3被拒绝 -2表示未充值,-4账户关闭,-5待分配审核,-6账户被删除 -7 待销售确认
        $post['company_name'] = Utility::trim($post['company_name']);
        $post['company_address'] = Utility::trim($post['company_address']);
        $post['user_industry'] = intval($post['user_industry']);
        $post['client_category'] = intval($post['client_category']);
        $post['user_category'] = intval($post['user_category']);
        $post['area_id'] = intval($post['area_id']);
        $post['contacter'] = Utility::trim($post['contacter']);
        $post['email'] = Utility::trim($post['email']);
        $post['notice_email'] = Utility::trim($post['notice_email']);
        $post['website'] = trim(Utility::trim($post['website']), '\\');
        $post['allowwebsite'] = trim(Utility::trim($post['allowwebsite']), '\\');
        $post['contacter_phone'] = Utility::trim($post['contacter_phone']);
        $post['mobile'] = Utility::trim($post['mobile']);
        $post['notice_mobile'] = Utility::trim($post['notice_mobile']);

        $updateUserData['company_name'] = $post['company_name'] ? $post['company_name'] : $userData['company_name'];
        $updateUserData['company_address'] = $post['company_address'] ? $post['company_address'] : $userData['company_address'];
        $updateUserData['user_industry'] = isset($post['user_industry']) ? $post['user_industry'] : $userData['user_industry'];
        $updateUserData['contacter'] = $post['contacter'] ? $post['contacter'] : $userData['contacter'];
        $updateUserData['email'] = $post['email'] ? $post['email'] : $userData['email'];
        $updateUserData['client_category'] = isset($post['client_category']) ? $post['client_category'] : $userData['client_category'];
        $updateUserData['user_category'] = isset($post['user_category']) ? $post['user_category'] : $userData['user_category'];
        $updateUserData['website'] = $post['website'] ? $post['website'] : $userData['website'];
        $updateUserData['allowwebsite'] = isset($post['allowwebsite']) ? $post['allowwebsite'] : $userData['allowwebsite'];
        $updateUserData['contacter_phone'] = $post['contacter_phone'] ? $post['contacter_phone'] : $userData['contacter_phone'];
        $updateUserData['mobile'] = $post['mobile'] ? $post['mobile'] : $userData['mobile'];
        $updateUserData['notice_email'] = $post['notice_email'] ? $post['notice_email'] : $userData['notice_email'];
        $updateUserData['notice_mobile'] = $post['notice_mobile'] ? $post['notice_mobile'] : $userData['notice_mobile'];
        $updateUserData['area_id'] = $post['area_id'] ? $post['area_id'] : $userData['area_id'];
        if ($post['client_category'] == 5) {
            $updateUserData['crm_custom_id'] = '';
        }

        $crmData = array(
            'server_type' => 'edit_user',
            'mg_id' => '',
            'custom_id' => $userData['crm_custom_id'],
            'company_address' => $updateUserData['company_address'],
            'contacter' => $updateUserData['contacter'],
            'email' => $updateUserData['email'],
            'contacter_phone' => $updateUserData['contacter_phone'],
            'mobile' => $updateUserData['mobile'],
            'user_name' => $userData['user_name'],
            'ad_user_id' => $intUserId,
            //无更新信息  接口需优化
            //'parent_user_id' => $crmAdminUserId,
            'client_category' => $updateUserData['client_category'],
            'user_category' => $updateUserData['user_category'],
            'area_id' => $updateUserData['area_id'],
            'user_industry' => $updateUserData['user_industry'],
            'company_name' => $updateUserData['company_name'],
            'website' => $updateUserData['website'],
        );

        $flag = ($crmData['client_category'] == $userData['client_category'] && $crmData['client_category'] == 5) ? false : true;
        if ($crmData['client_category'] == 2 || $crmData['client_category'] == 7) {
            $flag = false;
        }

        $crm_code = 0;
        if (!defined('DJ_DISABLED_CRM') && $flag) {
            $crmRet = $this->_postUserDataToCrm($crmData);
            if (isset($crmRet) && !empty($crmRet)) {
                $crmRet = json_decode($crmRet, true);
                if ($crmRet['code'] == 1) {
                    $crm_code = 1;
                } else {
                    return array(
                        'errno' => DJAPI_EC_ACCOUNT_CRM_ERROR,
                        'data' => $crmRet['message']
                    );
                }
                if ($crmData['custom_id'] && $updateUserData['company_name'] != $userData['company_name']) {
                    $crmUserUpdateInfo['company_name'] = $crmData['company_name'];
                    $this->_updateCrmUserInfo($crmData['custom_id'], $crmUserUpdateInfo, $intUserId);
                }
            }
        }

        if (defined('DJ_DISABLED_CRM') || $crm_code == 1 || !$flag) {
            $this->_updateUser($intUserId, $updateUserData);
            if ($post['user_channel']) {
                $this->_updateUserChannel($intUserId, $post['user_channel']);
            }

            $mqData = $updateUserData;
            $mqData['ad_user_id'] = $intUserId;
            $mqData['logid'] = $logid;
            $mqData['ip_address'] = isset($_SERVER['HTTP_CLIENTIP']) ? $_SERVER['HTTP_CLIENTIP'] : '';
            $mqData['opt_user_id'] = isset($_SERVER['HTTP_OPTUSERID']) ? $_SERVER['HTTP_OPTUSERID'] : 0;

            CEmqPublisher::send(
                Yii::app()->params['exchange']['updateUserEapi'],
                __CLASS__ . '_' . __FUNCTION__,
                json_encode($mqData),
                $logid,
                Yii::app()->params['emq']
            );
        }

        return array(
            'errno' => DJAPI_EC_SUCCESS,
            'data' => 'ok'
        );
    }

}
