<?php
/**
 * 默认的控制器
 * 当然, 默认的控制器, 动作, 模块都是可用通过配置修改的
 * 也可以通过$dispater->setDefault*Name来修改
 */
class UserController extends Yaf_Controller_Abstract {

    public function indexAction() {
        $methods = get_class_methods(get_class($this));
        foreach ($methods as $m) {
            if ((substr($m, -6) == 'Action') && ($m != 'indexAction')) {
                $actions[] = $m;
            }
        }
        $view = $this->getView();
        $view->assign("errno", EAPI_SUCCESS);
        $view->assign("data", $actions);
        $view->display(APPLICATION_PATH."/views/function.phtml");
    }

    /*
     *  @api
    */

	public function getChannelTypeAction() {
        $userId = Eapi_Checker::getParam('userId', EAPI_PARAM_USER_ID_NULL);

        $params = Yaf_Registry::get("params");
        $params = Eapi_Checker::checkParams($params);

        $model = UserModel::getInstance();

        $channel = $model->getChannel_cache($userId);
        if ($channel) {
            $channel = implode('|', $channel);
        }

        $this->getView()->assign("errno", EAPI_SUCCESS);
        $this->getView()->assign("data", $channel);
	}

    /**
     * @api
     * 返回账户详细信息
     */
    public function getDetailAction ()
    {
        $userId = Eapi_Checker::getParam('userId', EAPI_PARAM_USER_ID_NULL);

        $params = Yaf_Registry::get("params");
        $params = Eapi_Checker::checkParams($params);

        $isDelete = Eapi_Checker::getParam('isDelete', 0, 0);
        $isDelete = Eapi_Checker::assert_int($isDelete, EAPI_PARAM_USER_ID_INVALID);

        $model = UserModel::getInstance();

        $userInfo = $model->getUserInfo_cache($userId);
        if (!$userInfo){
            throw new Eapi_Exception(EAPI_USER_NOT_EXIST);
        } elseif (!isset($userInfo['status'])) {
            throw new Eapi_Exception(EAPI_USER_NOT_EXIST);
        } else {
            if (($isDelete == 0) && ($userInfo['status'] == -6)) {
                throw new Eapi_Exception(EAPI_USER_NOT_EXIST);
            } elseif (($isDelete == 1) && ($userInfo['status'] != -6)) {
                throw new Eapi_Exception(EAPI_USER_NOT_EXIST);
            }
        }
        $website = trim(str_replace(";", ",", $userInfo["website"]), ",");
        $allowwebsite = trim(str_replace(";", ",", $userInfo["allowwebsite"]), ",");
        $allowDomain = implode(",", array_filter(array(
            $website,
            $allowwebsite
        )));
        $userInfo['allowDomain'] = $allowDomain;

        $view = $this->getView();
        $view->assign("errno", EAPI_SUCCESS);
        $view->assign("data", $userInfo);

    }

    /*
     *  @api
     */

    public function updateAction()
    {
        $userId = Eapi_Checker::getParam('userId', EAPI_PARAM_USER_ID_NULL);
        $userId = Eapi_Checker::assert_int($userId, EAPI_PARAM_USER_ID_INVALID);
        $params = Yaf_Registry::get("params");
        $params = Eapi_Checker::checkParams($params);

        $model = UserModel::getInstance();
        $model->checkStatus($userId, true);
        $model->updateDetail_decache($userId, $params);

        $view = $this->getView();
        $view->assign("errno", EAPI_SUCCESS);
        $view->assign("data", 'ok');
    }


}
