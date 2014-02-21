<?php
/**
 * 默认的控制器
 * 当然, 默认的控制器, 动作, 模块都是可用通过配置修改的
 * 也可以通过$dispater->setDefault*Name来修改
 */
class PlanController extends Yaf_Controller_Abstract {

    public function indexAction() {
        $methods = get_class_methods(get_class($this));
        foreach ($methods as $m) {
            if ((substr($m, -6) == 'Action') && ($m != 'indexAction')) {
                $actions[] = $m;
            }
        }
        $this->getView()->assign("errno", EAPI_SUCCESS);
        $this->getView()->assign("data", $actions);
    }

    public function getInfosByUserIdAction ()
    {
        $userId = Eapi_Checker::getParam('userId', EAPI_PARAM_USER_ID_NULL);
        Checker::assert_int($userId, EAPI_PARAM_USER_ID_INVALID);
        $splitId = Eapi_Checker::getParam('splitId', EAPI_PARAM_SPLIT_ID_NULL);
        Checker::assert_int($splitId, EAPI_PARAM_SPLIT_ID_INVALID);

//        $user_type = $this->getUserType($uid);
        $model = PlanModel::getInstance();
        if (empty($type)) {
            $adplanList = $model->getInfosByUserId_cache($userId);
        } elseif (in_array($type, $user_type)) {
            $adplanList = $this->_getPlanList($userId, array($type));
        } else {
            throw new Eapi_Exception(DJAPI_EC_DATABASE,' not match user type');
        }

        $view = $this->getView();
        $view->assign("errno", EAPI_SUCCESS);
        $view->assign("data", $adplanList);

    }


    public function getInfoAction ()
    {
        $splitId = Eapi_Checker::getParam('splitId', EAPI_PARAM_SPLIT_ID_NULL);
        Eapi_Checker::assert_int($splitId, EAPI_PARAM_SPLIT_ID_INVALID);
        $planId = Eapi_Checker::getParam('planId', EAPI_PARAM_PLAN_ID_NULL);
        $planId = Eapi_Checker::assert_int($planId, EAPI_PARAM_PLAN_ID_INVALID);
        $type = Eapi_Checker::getParam('channelType', 0, '');

        $model = PlanModel::getInstance();

//        if (!empty($type) && ! $model->checkPlanId($planId, $uid, $type)) {
//            throw new DjApiException(EAPI_PLAN_NOT_EXIST, '_param.planID');
//        } elseif (! $model->checkPlanId($planId, $userId)) {
//            throw new DjApiException(EAPI_PLAN_NOT_EXIST, '_param.planID');
//        }

        $PlanInfo = $model->getInfo_cache($planId);
        if ($PlanInfo) {
            if ($PlanInfo['status'] == - 1) {
                throw new Eapi_Exception(EAPI_PLAN_NOT_EXIST);
            }
            $edc_service = $model->getServiceTime_cache($planId);
            if ($edc_service) {
                foreach ($edc_service as $k => $v) {
                    $info['week'] = intval($k);
                    foreach ($v as $s => &$t) {
                        $t = intval($t);
                    }
                    unset($t);
                    $info['hour'] = $v;
                    $serviceinfo[] = $info;
                }
            }
            $Areaidlist = $model->getServiceAreas_cache($planId);
            if ($Areaidlist) {
                foreach ($Areaidlist as $s => $t) {
                    $AreaidInfo[] = intval($t);
                }
            } else {
                $Areaidlist = '';
            }
        } else {
            throw new Eapi_Exception(EAPI_PLAN_NOT_EXIST);
        } // 相应信息不存在
        $PlanInfo['region'] = json_encode($AreaidInfo);
        $PlanInfo['schedule'] = json_encode($serviceinfo);

        $view = $this->getView();
        $view->assign("errno", EAPI_SUCCESS);
        $view->assign("data", $PlanInfo);

    }

    /**
     * 批量获取计划
     * @param string idList json格式的计划id数组，如[2,3]
     * @return array
     */
    public function getInfosAction(){
        $splitId = Eapi_Checker::getParam('splitId', EAPI_PARAM_SPLIT_ID_NULL);
        Eapi_Checker::assert_int($splitId, EAPI_PARAM_SPLIT_ID_INVALID);
        $planIds = Eapi_Checker::getParam('planIds', EAPI_PARAM_PLAN_IDS_NULL);
        $planIds = Eapi_Checker::assert_json($planIds, EAPI_PARAM_PLAN_IDS_INVALID);
        $type = Eapi_Checker::getParam('channelType', 0, '');

        $model = PlanModel::getInstance();
        $arrId = null;
        $planIds = array_unique($planIds);
        /**
         * 验证数量
         */
        if (count($planIds) > 100) {
            throw new Eapi_Exception(DJAPI_EC_CAMPAIGNS_TOO_MANY);
        }
        /**
         * 查询信息
         */
        $planBaseInfos = $model->getInfos_batchcache($planIds);
        /**
         * 合并数据
         */
        $planInfos = array();
        foreach ($planBaseInfos as $planBaseInfo) {
            $planId = $planBaseInfo["id"];

            $edc_service = $model->getServiceTime_cache($planId);
            if ($edc_service) {
                foreach ($edc_service as $k => $v) {
                    $info['week'] = intval($k);
                    foreach ($v as $s => &$t) {
                        $t = intval($t);
                    }
                    unset($t);
                    $info['hour'] = $v;
                    $serviceinfo[] = $info;
                }
            }
            $planBaseInfo["schedule"] = json_encode($serviceinfo);

            $Areaidlist = $model->getServiceAreas_cache($planId);
            if ($Areaidlist) {
                foreach ($Areaidlist as $s => $t) {
                    $AreaidInfo[] = intval($t);
                }
            } else {
                $Areaidlist = '';
            }
            $planBaseInfo["region"] = json_encode($AreaidInfo);

            $planInfos[] = $planBaseInfo;

        }

        $view = $this->getView();
        $view->assign("errno", EAPI_SUCCESS);
        $view->assign("data", $planInfos);

    }

}
