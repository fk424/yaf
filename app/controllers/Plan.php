<?php
/**
 * 默认的控制器
 * 当然, 默认的控制器, 动作, 模块都是可用通过配置修改的
 * 也可以通过$dispater->setDefault*Name来修改
 */
class PlanController extends Eapi_ControllerBase {

    /*
     * todo:
     */
    public function addAction()
    {
        $required = array('userId', 'type', 'title', 'budget', '');
        $optional = array(
            'email','companyAddress', 'contracter','contracterPhone',
            'mobile','companyName','website'
            );

    }

    // 第二版计划添加
    public function add2 ()
    {
        $objReqParam = DjApiRequestParam::instance();
        if (!YII_DEBUG && $objReqParam->getRequestType() != 'POST') {
            throw new DjApiException(DJAPI_EC_BAD_REQUEST_TYPE);
        }
        $params = $objReqParam->getRequest();
        $uid = $objReqParam->getUserId();
        $logid  = $objReqParam->getParam('logid');
        if (empty($logid)) $logid = Utility::getLoggerID('campaign_add');

        /**
         * 获取参数
         */
        $name = isset($params['title']) ? Utility::trim($params['title']) : NULL;
        $budget = isset($params['exp_amt']) ? Utility::trim($params['exp_amt']) : NULL;
        $region = isset($params['region']) ? Utility::trim($params['region']) : NULL;
        $schedule = isset($params['schedule']) ? Utility::trim($params['schedule']) : NULL;
        $startDate = isset($params['start_date']) ? Utility::trim($params['start_date']) : NULL;
        $endDate = isset($params['end_date']) ? Utility::trim($params['end_date']) : NULL;
        $type = isset($params['type']) ? (int) $params['type'] : null;
        $token = isset($params['token']) ? $params['token'] : null;

        /**
         * 初始化计划数据
         */
        $postData = array(
            'ad_service' => 1,
            'create_time' => time(),
            'update_time' => time(),
            'ad_user_id' => $uid
        );

        /**
         * 验证数据合法性
         */
        // 推广计划名称
        if (! is_null($name) && ! empty($name)) {
            $nameLen = Utility::strlenAsGBK($name);
            if ($nameLen > 32) {
                throw new DjApiException(DJAPI_EC_CAMPAIN_EXCEED_LENGTH);
            }
            $CheckName = $this->_checkPlanName($uid, $name);
            if ($CheckName['errno'] == 0 && $CheckName['msg']['data']['valid'] != 1) {
                throw new DjApiException(DJAPI_EC_DUPLICATE_CAMPAIN_NAME);
            }
            $postData['title'] = $name;
        } else {
            throw new DjApiException(DJAPI_EC_CAMPAIN_NAME_NULL);
        }
        // 推广计划每日预算
        if (! is_null($budget)) {
            if ( $budget !== 0 && $budget !== '0' && ! preg_match('/^[1-9][0-9]*$/', $budget)) {
                throw new DjApiException(DJAPI_EC_CAMPAIGN_BUDGET_IS_VALID);
            }
            if ($budget < 10 && $budget != 0) {
                throw new DjApiException(DJAPI_EC_CAMPAIGN_BELOW_MINI);
            }
            if ($budget > 999999.99) {
                throw new DjApiException(DJAPI_EC_CAMPAIGN_EXCEED_MAX);
            }
            $postData['exp_amt'] = $budget;
        } else {
            $postData['exp_amt'] = 0;
        }
        // 投放时间
        if (! is_null($schedule) && ! empty($schedule)) {
            try {
                $schedule = json_decode($schedule, true);
            } catch (Exception $e) {
                throw new DjApiException(DJAPI_EC_CAMPAIGN_SCHEDULE_SETTING);
            }
            if (! is_array($schedule)) {
                throw new DjApiException(DJAPI_EC_CAMPAIGN_SCHEDULE_SETTING);
            }
            if (! $this->_getServiceValid($schedule)) {
                throw new DjApiException(DJAPI_EC_CAMPAIGN_SCHEDULE_SETTING);
            }
            $schedule = $this->_formatService($schedule);
        }
        if (!$schedule) {
            $postData['ad_service'] = 0;
            for ($week = 1; $week <= 7; $week ++) {
                for ($hour = 0; $hour < 24; $hour ++) {
                    $schedule[$week][] = $hour;
                }
            }
        }
        $postServiceData['plan_service'] = json_encode($schedule);
        // 检查地域
        if (! is_null($region) && ! empty($region)) {
            if (trim($region) == NULL) {
                throw new DjApiException(DJAPI_EC_CAMPAIGN_REGION);
            }
            if (! $region) {
                $areaList = array(
                    0
                );
            } else {
                // json格式地域信息
                if (! preg_match('/^\[[0-9]{1,5}(,[0-9]{1,5})*\]$/', $region)) {
                    throw new DjApiException(DJAPI_EC_CAMPAIGN_REGION);
                }
                $areaList = json_decode($region, true);
                if (! is_array($areaList)) {
                    throw new DjApiException(DJAPI_EC_CAMPAIGN_REGION);
                }
                $areaList = array_map('intval', $areaList);
                $oldCount = count($areaList);
                if ($areaList) {
                    $areaList = array_filter($areaList, 'self::_getRegionValid');
                }
                if ($oldCount != count($areaList)) {
                    throw new DjApiException(DJAPI_EC_CAMPAIGN_REGION);
                }
            }
        } else {
            $areaList = array(
                0
            );
        }
        $postRegionData['area_code'] = json_encode($areaList);
        // 检查开始时间
        if (! is_null($startDate) && ! empty($startDate)) {
            if (! $this->is_date($startDate)) {
                throw new DjApiException(DJAPI_EC_CAMPAIGN_INVALID_DATE);
            }
            $startDate = strtotime($startDate);
        } else {
            $startDate = time();
        }
        $postData['start_date'] = $startDate;
        // 检查结束时间
        if (! is_null($endDate) && ! empty($endDate)) {
            if (! $this->is_date($endDate)) {
                throw new DjApiException(DJAPI_EC_CAMPAIGN_INVALID_DATE);
            }
            $endDate = strtotime($endDate . ' 23:59:59');
        } else {
            $endDate = strtotime(Yii::app()->params['endTime']);
        }
        $postData['end_date'] = $endDate;
        // 开始时间要大于昨天这个时候
        if ($startDate < time() - 86400) {
            throw new DjApiException(DJAPI_EC_CAMPAIGN_DATE_RANGE);
        }
        // 开始时间<结束时间
        if ($endDate < $startDate) {
            throw new DjApiException(DJAPI_EC_CAMPAIGN_DATE_RANGE);
        }
        // 计划类型
        if (!is_null($type) && !empty($type)) {
            if (!in_array($type, array(1,2,6))) {
                throw new DjApiException(DJAPI_EC_CAMPAIGN_INVALID_TYPE);
            }
        } else {
            throw new DjApiException(DJAPI_EC_CAMPAIGN_TYPE_NULL);
        }
        // 如果$type为特殊搜索类型，需要验证是否有投放权限
        if ($type == 6) {
            if (!Utility::checkUserSearchAdPermission($uid, $type)) {
                throw new DjApiException(DJAPI_EC_CAMPAIGN_INVALID_TYPE);
            }
        }
        $postData['type'] = $type;

        // 计划是否超出限额
        $res = $this->_checkPlanNum($uid);
        if ($res['errno'] == -1) {
            throw new DjApiException(DJAPI_EC_DATABASE, 'db connect fail');
        } elseif ($res['errno'] == 1) {
            throw new DjApiException(DJAPI_EC_CAMPAIGNS_TOO_MANY, array('max_num' => $res['max_num']));
        }

        /**
         * 数据库新增记录
         */
        if (! is_null($token)) {
            $postData['token'] = $token;
        }
        $res = Utility::edcApiPost('plan/AddOpt', $postData);
        if ($res['errno'] == 0 && $res['msg']['id'] > 0) {
            $postData['id'] = $res['msg']['id'];

            //新增地域及投放时段记录
            if (isset($postRegionData['area_code']) && isset($postServiceData['plan_service'])) {
                //$postRegionData['area_code'] = null;
                //$postServiceData['plan_service'] = null;
                $updateAreaFail = $updateServiceFail = false;
                $multiData['area'] = array(
                    'url'  => Yii::app()->params['edcApi']['url'] . 'plan/AddAreaOpt',
                    'data' => array(
                        'plan_id'   => $postData['id'],
                        'area_code' => $postRegionData['area_code']
                    )
                );
                $multiData['service'] = array(
                    'url'  => Yii::app()->params['edcApi']['url'] . 'plan/AddServiceOpt',
                    'data' => array(
                        'plan_id' => $postData['id'],
                        'service' => $postServiceData['plan_service']
                    )
                );
                $res = Utility::multiEdcPost($multiData);
                if (isset($res[0]['errno']) && $res[0]['errno'] == 0) {
                    $postData['area_code'] = $postRegionData['area_code'];
                } else {
                    $updateAreaFail = true;
                    $postData['area_code'] = '';
                }
                if (isset($res[1]['errno']) && $res[1]['errno'] == 0) {
                    $postData['plan_service'] = $postServiceData['plan_service'];
                } else {
                    $updateServiceFail = true;
                    $postData['plan_service'] = '';
                }
            } else {
                $updateAreaFail = $updateServiceFail = true;
                $postData['area_code'] = $postData['plan_service'] = '';
            }

            $mqData = $postData;
            $mqData['status'] = 1;
            $mqData['split_db_id'] = $uid;
            $mqData['logid']       = $logid;
            $mqData['ip_address']  = isset($_SERVER['HTTP_CLIENTIP']) ? $_SERVER['HTTP_CLIENTIP'] : '';
            $mqData['opt_user_id'] = isset($_SERVER['HTTP_OPTUSERID']) ? $_SERVER['HTTP_OPTUSERID'] : 0;
            $mqData['source']      = Utility::mqMsgSource($logid);
            CEmqPublisher::send(
                Yii::app()->params['exchange']['newPlan'],
                __CLASS__ . '_' . __FUNCTION__,
                json_encode($mqData),
                $logid,
                Yii::app()->params['emq']
            );

            if ($updateAreaFail == true && $updateServiceFail == true) {
                $errno = DJAPI_EC_CAMPAIGN_AREA_SERVICE_FAIL; //地域、投放时段均新建失败
            } elseif ($updateAreaFail == true) {
                $errno = DJAPI_EC_CAMPAIGN_AREA_FAIL; //地域新建失败
            } elseif ($updateServiceFail == true) {
                $errno = DJAPI_EC_CAMPAIGN_SERVICE_FAIL; //投放时段新建失败
            } else {
                $errno = DJAPI_EC_SUCCESS;
            }

            return array(
                'errno' => $errno,
                'data' => array(
                    'id' => $postData['id']
                )
            );
        } else {
            throw new DjApiException(DJAPI_EC_DATABASE, 'eapi/AddPlan fail');
        }
    }
    /*
     * todo:
     */
    public function updateAction()
    {

    }

    /*
     * todo:
     */
    public function updateStatusAction()
    {

    }

    /*
     * todo:
     */
    public function updateBudgetAction()
    {

    }

    /*
     * todo:
     */
    public function deleteAction()
    {

    }

    public function getInfosByUserIdAction ()
    {
        $userId = Eapi_Checker::getParam('userId', EAPI_PARAM_USER_ID_NULL);
        $splitId = Eapi_Checker::getParam('splitId', EAPI_PARAM_SPLIT_ID_NULL);

//        $user_type = $this->getUserType($uid);
        $model = PlanModel::getInstance();

        $params = Yaf_Registry::get("params");
        $params = PlanModel::checkParams($params);

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
        $planId = Eapi_Checker::getParam('planId', EAPI_PARAM_PLAN_ID_NULL);
        $type = Eapi_Checker::getParam('channelType', 0, '');

        $model = PlanModel::getInstance();

        $params = Yaf_Registry::get("params");
        $params = PlanModel::checkParams($params);

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
        $planIds = Eapi_Checker::getParam('planIds', EAPI_PARAM_PLAN_IDS_NULL);
        $type = Eapi_Checker::getParam('channelType', 0, '');

        $model = PlanModel::getInstance();
        $params = Yaf_Registry::get("params");
        $params = PlanModel::checkParams($params);
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
