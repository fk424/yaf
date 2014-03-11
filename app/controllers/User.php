<?php
/**
 * 默认的控制器
 * 当然, 默认的控制器, 动作, 模块都是可用通过配置修改的
 * 也可以通过$dispater->setDefault*Name来修改
 */
class UserController extends Eapi_ControllerBase {
    
    /*
     *  @api
    */

	public function getChannelTypeAction() {
        $userId = Eapi_Checker::getParam('userId', EAPI_PARAM_USER_ID_NULL);

        $params = Yaf_Registry::get("params");
        $params = PlanModel::checkParams($params);

        $model = UserModel::getInstance();
        
        $model->checkStatus($userId, true);

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
        $params = PlanModel::checkParams($params);

        $isDelete = Eapi_Checker::getParam('isDelete', 0, 0);
        $isDelete = Eapi_Checker::assert_int($isDelete, EAPI_PARAM_USER_ID_INVALID);

        $model = UserModel::getInstance();
        $model->checkStatus($userId, true);

        $userInfo = $model->getUserInfo_cache($userId);

        // if (!$userInfo){
        //     throw new Eapi_Exception(EAPI_USER_NOT_EXIST);
        // } elseif (!isset($userInfo['status'])) {
        //     throw new Eapi_Exception(EAPI_USER_NOT_EXIST);
        // } else {
        //     if (($isDelete == 0) && ($userInfo['status'] == -6)) {
        //         throw new Eapi_Exception(EAPI_USER_IS_DELETED);
        //     } elseif (($isDelete == 1) && ($userInfo['status'] != -6)) {
        //         throw new Eapi_Exception(EAPI_USER_NOT_EXIST);
        //     }
        // }
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
        $required = array('userId');
        $optional = array(
            'email','companyAddress', 'contracter','contracterPhone',
            'mobile','companyName','website'
            );
        $userId = Eapi_Checker::getParam('userId', EAPI_PARAM_USER_ID_NULL);
        $params = Yaf_Registry::get("params");
        $required_params = UserModel::checkParams($params, $required);
        $optional_params = UserModel::checkParams($params, $optional);

        $model = UserModel::getInstance();
        $model->checkStatus($userId, true);
        $model->updateDetail_decache($userId, $optional_params);

        $view = $this->getView();
        $view->assign("errno", EAPI_SUCCESS);
        $view->assign("data", 'ok');
    }

    /*
     *  @api
     */

    public function updateStatusAction()
    {
        $reqiured = array('userId','status');
        $userId = Eapi_Checker::getParam('userId', EAPI_PARAM_USER_ID_NULL);
        $status = Eapi_Checker::getParam('status', EAPI_PARAM_USER_STATUS_NULL);
        $params = Yaf_Registry::get("params");
        $params = UserModel::checkParams($params, $reqiured);

        $model = UserModel::getInstance();

        $model->checkStatus($userId, true);

//        Eapi_Checker::assert_trans()

        $model->updateDetail_decache($userId, $params);

        $view = $this->getView();
        $view->assign("errno", EAPI_SUCCESS);
        $view->assign("data", 'ok');
    }
    /*
     * todo:修改账户预算
     */
    public function updateBudgetAction() 
    {


    }

    /*
     * todo:
     */
    public function updateChannelTypeAction() 
    {


    }

}
