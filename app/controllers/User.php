<?php
/**
 * 默认的控制器
 * 当然, 默认的控制器, 动作, 模块都是可用通过配置修改的
 * 也可以通过$dispater->setDefault*Name来修改
 */
class UserController extends Yaf_Controller_Abstract {

	public function getChannelTypeAction() {
		$params = $_GET;
		$userId = isset($params['userId']) ? $params['userId'] : 0;

        $model = UserModel::getInstance();

        $channel = $model->getChannel($userId);
        if ($channel) {
            $channel = implode('|', $channel);
        }

        $return = array(
             'errno' => DJAPI_EC_SUCCESS,
             'data' => $channel
        );
        $this->getView()->assign("errno", DJAPI_EC_SUCCESS);
        $this->getView()->assign("data", $channel);
	}

    /**
     * 返回账户详细信息
     */
    public function getDetailAction ()
    {
        $params = $_GET;
        $userId = isset($params['userId']) ? $params['userId'] : 0;

        $model = new UserModel();

        $userInfo = $model->getUserInfo($userId);
        $website = trim(str_replace(";", ",", $userInfo["website"]), ",");
        $allowwebsite = trim(str_replace(";", ",", $userInfo["allowwebsite"]), ",");
        $allowDomain = implode(",", array_filter(array(
            $website,
            $allowwebsite
        )));
        $userInfo['allowDomain'] = $allowDomain;

        $this->getView()->assign("errno", DJAPI_EC_SUCCESS);
        $this->getView()->assign("data", $userInfo1);

    }

}
