<?php

/**
 * This is the model class for table "ad_plan".
 *
 * The followings are the available columns in table 'ad_plan':
 * @property integer $id
 * @property integer $ad_user_id
 * @property integer $type
 * @property string $title
 * @property string $exp_amt
 * @property integer $ad_service
 * @property integer $start_date
 * @property integer $end_date
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $admin_order_id
 */
class PlanModel extends Eapi_ModelBase
{
    static protected $_instance;

    protected $_cache_func = array(
        'getInfosByUserId' => 'plan:infosByUserId',
        'getInfo' => 'plan:info',
        'getInfos' => 'plan:info',
        'getServiceTime' => 'plan:service',
        'getServiceAreas' => 'plan:area',
        );

    const PLAN_TYPE_SHOW    = 1;
    const PLAN_TYPE_SEARCH  = 2;
    const PLAN_TYPE_EGOODS  = 6;
    const PLAN_TYPE_APPS    = 7; /*手机助手*/

    const PLAN_STATUS_ENABLE = 1;
    const PLAN_STATUS_PAUSE  = 0;
    const PLAN_STATUS_DELETE = -1;

    static public function checkParams($params, $keys = null)
    {
        $params = parent::checkParams($params, $keys);//print_r($params);die('sdf');
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
                default:
                    break;
            }

        }
        return $params;

    }
    public function getInfosByUserId ($uid)
    {
        $postData = array(
            'userID' => $uid
        );
        $res = Utility::edcApiPost('eapi/SearchUserPlan', $postData);
        if ($res['errno'] == 0 && $res['msg']['data']) {
            foreach ($res['msg']['data'] as $k => $v) {
                if ($v['status'] == 1 || $v['status'] == 0) {
//                    if (in_array($v['type'], $type)) {
                        $return[] = array("id"=>$v['id'],"title"=>$v['title'], "type" => $v['type']);
//                    }
                }
            }
        } else {
            $return = array();
        }
        return $return;
    }


    public function getInfos($idList)
    {
        $edcData = array(
            'planIDs' => json_encode($idList),
            'token' => 'plan_searchByIds_'.json_encode($idList)
        );
        $res = Utility::edcApiPost('plan/searchByIds', $edcData);
        if (isset($res['errno']) && $res['errno'] == 0) {
            $return = $res['msg']['data'];
        } else {
            $return = false;
        }

        return $return;
    }



    public function getServiceAreas ($plan_id)
    {
        $postData = array(
            'planID' => $plan_id
        );
        $res = Utility::edcApiPost('eapi/PlanArea', $postData);
        if ($res['errno'] == 0 && $res['msg']['data']) {
            $res = $res['msg']['data'];
            return $res;
        } else {
            return false;
        }
    }

    public function getInfo ($plan_id)
    {
        $postData = array(
            'planID' => $plan_id,
            'status' => json_encode(array(
                0,
                1
            ))
        );
        $res = Utility::edcApiPost('eapi/SearchPlan', $postData);
        if ($res['errno'] == 0 && $res['msg']['data']) {
            $PlanInfo = $res['msg']['data'];
            $PlanInfo['start_date'] = date("Y-m-d", $PlanInfo['start_date']);
            $PlanInfo['end_date'] = date("Y-m-d", $PlanInfo['end_date']);
            unset($PlanInfo['ad_user_id']);
            unset($PlanInfo['admin_order_id']);
            unset($PlanInfo['old_plan_id']);
            return $PlanInfo;
        } else {
            return false;
        }
    }

    /**
     * 根据计划ID获取PlanService
     */
    public function getServiceTime ($planId)
    {
        if (empty($planId)) return false;
        $planId = (int) $planId;
        if ($planId <= 0) return false;

        $res = Utility::edcApiPost('eapi/PlanService', array(
            'planID' => $planId
        ));
        if ($res['errno'] != 0) {
            throw new Eapi_Exception(DJAPI_EC_DATABASE);
        }
        return $res['msg']['data'];
    }
}
