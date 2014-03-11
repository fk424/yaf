<?php
Class UserTest extends Yaf_Controller_TestCase {

    /**
     */
    public function testgetDetailAction() {
        $controller = self::getController();
        $action = self::getAction(__FUNCTION__);

        /*  参数校验 */
        //参数：userId校验
        $response = requestActionAndParseBody($controller,$action);
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('userId' => null));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('userId' =>''));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller, $action, array('userId' =>'a'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_INVALID, $data['errno']);

        /*  业务校验    */
        //用户状态校验
        $response = requestActionAndParseBody($controller, $action, array('userId' =>2480646281, 'email' => 'fk424@263.net'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_USER_NOT_EXIST, $data['errno']);

        UserModel::getInstance()->updateDetail_decache('248064628', array('status' => -6));
        $response = requestActionAndParseBody($controller, $action, array('userId' =>248064628, 'email' =>'fk424@263.net'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_USER_IS_DELETED, $data['errno']);
        UserModel::getInstance()->updateDetail_decache('248064628', array('status' => 1));
        $user = UserModel::getInstance()->getUserInfo_cache('248064628');
        /*  返回格式校验  */
        $response = requestActionAndParseBody($controller,$action, array('userId'=>248064628));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(0, $data['errno']);
        $this->assertEquals(248064628, $data['data']['id']);

    }

    public function testgetChannelTypeAction(){
        $controller = self::getController();
        $action = self::getAction(__FUNCTION__);

        /*  参数校验    */
        //参数：userId校验
        $response = requestActionAndParseBody($controller,$action);
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('userId' => null));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('userId' =>''));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller, $action, array('userId' =>'a'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_INVALID, $data['errno']);

        /*  业务校验    */
        //用户状态校验
        $response = requestActionAndParseBody($controller, $action, array('userId' =>2480646281, 'email' => 'fk424@263.net'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_USER_NOT_EXIST, $data['errno']);

        UserModel::getInstance()->updateDetail_decache('248064628', array('status' => -6));
        $response = requestActionAndParseBody($controller, $action, array('userId' =>248064628, 'email' =>'fk424@263.net'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_USER_IS_DELETED, $data['errno']);
        UserModel::getInstance()->updateDetail_decache('248064628', array('status' => 1));

        //todo:无channelType信息

        /*  返回格式校验  */
        $response = requestActionAndParseBody($controller,$action, array('userId'=>248064628));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(0, $data['errno']);

    }

    public function testupdateAction(){
        $controller = self::getController();
        $action = self::getAction(__FUNCTION__);

        /*  参数校验    */
        //参数：userId校验
        $response = requestActionAndParseBody($controller,$action);
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('userId' => null));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('userId' =>''));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller, $action, array('userId' =>'a'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_INVALID, $data['errno']);

        //缺少可选参数
        $response = requestActionAndParseBody($controller, $action, array('userId' =>'248064628'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_OPTIONAL_PARAM_NULL, $data['errno']);

        //可选参数格式校验
        $response = requestActionAndParseBody($controller, $action, array('userId' =>'248064628', 'email' =>'fk424'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_EMAIL_INVALID_EMAIL, $data['errno']);

        $response = requestActionAndParseBody($controller, $action, array('userId' =>'248064628', 'mobile' =>'fk424'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_MOBILE_INVALID_MOBILE, $data['errno']);

        $response = requestActionAndParseBody($controller, $action, array('userId' =>'248064628', 'companyAddress' =>'fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424asdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdf'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_COMPANY_ADDRESS_TOO_LONG, $data['errno']);

        $response = requestActionAndParseBody($controller, $action, array('userId' =>'248064628', 'contracterPhone' =>'fk424'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_CONTRACTER_PHONE_INVALID_PHONE, $data['errno']);

        $response = requestActionAndParseBody($controller, $action, array('userId' =>'248064628', 'website' =>'fk424'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_WEBSITE_INVALID_URL, $data['errno']);

        $response = requestActionAndParseBody($controller, $action, array('userId' =>'248064628', 'contracter' =>'fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424asdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdf'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_CONTRACTER_TOO_LONG, $data['errno']);

        /*  业务校验    */
        //用户状态校验
        $response = requestActionAndParseBody($controller, $action, array('userId' =>2480646281, 'email' => 'fk424@263.net'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_USER_NOT_EXIST, $data['errno']);

        //用户已删除
        UserModel::getInstance()->updateDetail_decache('248064628', array('status' => -6));
        $response = requestActionAndParseBody($controller, $action, array('userId' =>248064628, 'email' =>'fk424@263.net'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_USER_IS_DELETED, $data['errno']);
        UserModel::getInstance()->updateDetail_decache('248064628', array('status' => 1));

        //用户已注销
        UserModel::getInstance()->updateDetail_decache('248064628', array('status' => -4));
        $response = requestActionAndParseBody($controller, $action, array('userId' =>248064628, 'email' =>'fk424@263.net'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_USER_IS_CANCELED, $data['errno']);
        UserModel::getInstance()->updateDetail_decache('248064628', array('status' => 1));

        /*  返回格式校验  */
        $response = requestActionAndParseBody($controller,$action, array('userId'=>248064628, 'email' => 'fk424@263.net'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(0, $data['errno']);

    }

    public function testupdateStatusAction(){
        $controller = self::getController();
        $action = self::getAction(__FUNCTION__);

        /*  参数校验    */
        //参数：userId校验
        $response = requestActionAndParseBody($controller,$action);
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('userId' => null));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('userId' =>''));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller, $action, array('userId' =>'a', 'status' => 'a'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_INVALID, $data['errno']);

        //参数：status校验
        $response = requestActionAndParseBody($controller, $action, array('userId' =>'a'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_STATUS_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller, $action, array('userId' =>'a', 'status' => null));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_STATUS_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller, $action, array('userId' =>'a', 'status' => ''));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_STATUS_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller, $action, array('userId' =>'248064628', 'status' => 'a'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_STATUS_INVALID, $data['errno']);

        /*  业务校验    */
        //用户状态校验
        $response = requestActionAndParseBody($controller, $action, array('userId' =>2480646281, 'status' => '1'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_USER_NOT_EXIST, $data['errno']);

        //用户已删除
        UserModel::getInstance()->updateDetail_decache('248064628', array('status' => -6));
        $response = requestActionAndParseBody($controller, $action, array('userId' =>248064628, 'status' => '1'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_USER_IS_DELETED, $data['errno']);
        UserModel::getInstance()->updateDetail_decache('248064628', array('status' => 1));

        // $response = requestActionAndParseBody($controller, $action, array('userId' =>'248064628', 'status' =>'fk424'));
        // $data     = json_decode($response, TRUE);
        // $this->assertInternalType('array', $data);
        // $this->assertEquals(EAPI_PARAM_USER_STATUS_INVALID_EMAIL, $data['errno']);
    }
}