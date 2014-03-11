<?php
Class PlanTest extends Yaf_Controller_TestCase {

    /**
     */
    public function testgetInfoAction() {
        $controller = self::getController();
        $action = self::getAction(__FUNCTION__);

        //参数校验
        $response = requestActionAndParseBody($controller,$action);
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_SPLIT_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('splitId' => null));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_SPLIT_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('splitId' =>''));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_SPLIT_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller, $action, array('planId' => '1','splitId' =>'a'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_SPLIT_ID_INVALID, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('splitId' =>'248064628'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_PLAN_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('splitId' =>'248064628', 'planId' => ''));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_PLAN_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('splitId' =>'248064628', 'planId' => 'a'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_PLAN_ID_INVALID, $data['errno']);

        //业务校验
        $response = requestActionAndParseBody($controller,$action, array('splitId' =>'248064628', 'planId' => '300237492'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PLAN_NOT_EXIST, $data['errno']);

        //todo: 计划被删除
        //todo: splitId错误
        //返回值格式校验
        $response = requestActionAndParseBody($controller,$action, array('splitId' =>'248064628', 'planId' => '30023749'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(0, $data['errno']);

    }

    public function testgetInfosAction() {
        $controller = self::getController();
        $action = self::getAction(__FUNCTION__);

        //参数校验
        $response = requestActionAndParseBody($controller,$action);
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_SPLIT_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('splitId' => null));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_SPLIT_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('splitId' =>''));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_SPLIT_ID_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller, $action, array('planIds' => '[1]','splitId' =>'a'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_SPLIT_ID_INVALID, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('splitId' =>'248064628'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_PLAN_IDS_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('splitId' =>'248064628', 'planIds' => ''));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_PLAN_IDS_NULL, $data['errno']);

        $response = requestActionAndParseBody($controller,$action, array('splitId' =>'248064628', 'planIds' => 'a'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(EAPI_PARAM_PLAN_IDS_INVALID, $data['errno']);

        //业务校验
        
        //返回值格式校验
        $response = requestActionAndParseBody($controller,$action, array('splitId' =>'248064628', 'planIds' => '[30023749,30023750]'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(0, $data['errno']);

    }

}
