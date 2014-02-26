<?php
Class UserTest extends PHPUnit_Framework_TestCase {

    private function generalParamTest($function) {
        //参数校验
        $response = requestActionAndParseBody('user',$function);
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody('user',$function, array('userId' =>''));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody('user', $function, array('userId' =>'a'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_ID_INVALID, $data['errno']);
    }

    private function generalBusinessTest($function) {
        $response = requestActionAndParseBody('user', $function, array('userId' =>2480646281));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_USER_NOT_EXIST, $data['errno']);
    }

    /**
     */
    public function testgetDetailAction() {
        $function = substr(substr(__FUNCTION__, 4), 0, -6);

        //参数校验
        $this->generalParamTest($function);

        //业务校验
        $this->generalBusinessTest($function);

        //返回格式校验
        $response = requestActionAndParseBody('user',$function, array('userId'=>248064628));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(0, $data['errno']);
        $this->assertEquals(248064628, $data['data']['id']);


    }

    public function testgetChannelTypeAction(){

        $function = substr(substr(__FUNCTION__, 4), 0, -6);

        //参数校验
        $this->generalParamTest($function);

        //业务校验
        $this->generalBusinessTest($function);

        //todo:无channelType信息

        //返回格式校验
        $response = requestActionAndParseBody('user',$function, array('userId'=>248064628));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(0, $data['errno']);

    }

    public function testupdateAction(){
        $function = substr(substr(__FUNCTION__, 4), 0, -6);

        //参数校验
        $this->generalParamTest($function);

        //todo: 缺少参数
        // $response = requestActionAndParseBody('user', $function, array('userId' =>'248064628'));
        // $data     = json_decode($response, TRUE);
        // $this->assertInternalType('array', $data);
        // $this->assertEquals(EAPI_PARAM_NULL, $data['errno']);

        $response = requestActionAndParseBody('user', $function, array('userId' =>'248064628', 'email' =>'fk424'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_EMAIL_INVALID_EMAIL, $data['errno']);

        $response = requestActionAndParseBody('user', $function, array('userId' =>'248064628', 'mobile' =>'fk424'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_MOBILE_INVALID_MOBILE, $data['errno']);

        $response = requestActionAndParseBody('user', $function, array('userId' =>'248064628', 'companyAddress' =>'fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424asdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdf'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_COMPANY_ADDRESS_TOO_LONG, $data['errno']);

        $response = requestActionAndParseBody('user', $function, array('userId' =>'248064628', 'contracterPhone' =>'fk424'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_CONTRACTER_PHONE_INVALID_PHONE, $data['errno']);

        $response = requestActionAndParseBody('user', $function, array('userId' =>'248064628', 'website' =>'fk424'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_WEBSITE_INVALID_URL, $data['errno']);

        $response = requestActionAndParseBody('user', $function, array('userId' =>'248064628', 'contracter' =>'fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424asdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdf'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_CONTRACTER_TOO_LONG, $data['errno']);

        $response = requestActionAndParseBody('user', $function, array('userId' =>'248064628', 'contracter' =>'fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424fk424asdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdf'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_USER_CONTRACTER_TOO_LONG, $data['errno']);

        //业务校验
        $this->generalBusinessTest($function);
        //todo:用户已删除
        //todo:用户已注销

        //返回格式校验
        $response = requestActionAndParseBody('user',$function, array('userId'=>248064628, 'email' => 'fk424@263.net'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(0, $data['errno']);

    }

}