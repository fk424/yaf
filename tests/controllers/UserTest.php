<?php
Class UserTest extends PHPUnit_Framework_TestCase {

    /**
     */
    public function testgetDetailAction() {
        $response = requestActionAndParseBody('user','getDetail', array('userId'=>248064628));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(0, $data['errno']);
        $this->assertEquals(248064628, $data['data']['id']);

        $response = requestActionAndParseBody('user','getDetail');
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(10101, $data['errno']);

        $response = requestActionAndParseBody('user','getDetail', array('userId' =>''));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(10101, $data['errno']);

        $response = requestActionAndParseBody('user', 'getDetail', array('userId' =>'a'));
        $data     = json_decode($response, TRUE);
        $this->assertInternalType('array', $data);
        $this->assertEquals(10102, $data['errno']);

    }
 
}