<?php
Class PlanTest extends PHPUnit_Framework_TestCase {

    private function generalParamTest($function) {
        //参数校验
        $response = requestActionAndParseBody('plan',$function);
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_SPLIT_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody('plan',$function, array('splitId' =>''));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_SPLIT_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody('plan', $function, array('splitId' =>'a'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_SPLIT_ID_INVALID, $data['errno']);
    }

    private function generalBusinessTest($function) {
        $response = requestActionAndParseBody('plan', $function, array('splitId' =>2480646281));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_USER_NOT_EXIST, $data['errno']);
    }

    /**
     */
    public function testgetInfoAction() {
        $function = substr(substr(__FUNCTION__, 4), 0, -6);

        //参数校验
        $this->generalParamTest($function);

        $response = requestActionAndParseBody('plan',$function, array('splitId' =>'248064628'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_PLAN_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody('plan',$function, array('splitId' =>'248064628', 'planId' => ''));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_PLAN_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody('plan',$function, array('splitId' =>'248064628', 'planId' => 'a'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_PLAN_ID_INVALID, $data['errno']);

        //业务校验
        $response = requestActionAndParseBody('plan',$function, array('splitId' =>'248064628', 'planId' => '300237492'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PLAN_NOT_EXIST, $data['errno']);

        //todo: 计划被删除
        //todo: splitId错误
        //返回值格式校验
        $response = requestActionAndParseBody('plan',$function, array('splitId' =>'248064628', 'planId' => '30023749'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(0, $data['errno']);

    }

    public function testgetInfosAction() {
        $function = substr(substr(__FUNCTION__, 4), 0, -6);

        //参数校验
        $this->generalParamTest($function);
        $response = requestActionAndParseBody('plan',$function, array('splitId' =>'248064628'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_PLAN_IDS_NULL, $data['errno']);

        $response = requestActionAndParseBody('plan',$function, array('splitId' =>'248064628', 'planIds' => ''));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_PLAN_IDS_NULL, $data['errno']);

        $response = requestActionAndParseBody('plan',$function, array('splitId' =>'248064628', 'planIds' => 'a'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_PLAN_IDS_INVALID, $data['errno']);

        //业务校验
        
        //返回值格式校验
        $response = requestActionAndParseBody('plan',$function, array('splitId' =>'248064628', 'planIds' => '[30023749,30023750]'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(0, $data['errno']);

    }

}
