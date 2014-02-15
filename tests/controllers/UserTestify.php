<?php
Class UserTestify{
    /**
     */
    public function testgetDetailAction() {
        //success
        tf()->test(__CLASS__.' '.__FUNCTION__, function(){
            $response = requestActionAndParseBody('user','getDetail', array('userId'=>248064628));
            $data     = json_decode($response, TRUE);
            tf()->assertEquals(0, $data['errno']);
            tf()->assertEquals(248064628, $data['data']['id']);
        });

        //userId类型错误
        tf()->test(__CLASS__.' '.__FUNCTION__, function(){
            $response = requestActionAndParseBody('user','getDetail', array('userId'=>'a'));
            $data     = json_decode($response, TRUE);
            tf()->assertEquals(EAPI_PARAM_USER_ID_INVALID, $data['errno']);
        });

        //缺少userId参数
        tf()->test(__CLASS__.' '.__FUNCTION__, function(){
            $response = requestActionAndParseBody('user','getDetail', array('userId'=>''));
            $data     = json_decode($response, TRUE);
            tf()->assertEquals(EAPI_PARAM_USER_ID_NULL, $data['errno']);
        });
        tf()->test(__CLASS__.' '.__FUNCTION__, function(){
            $response = requestActionAndParseBody('user','getDetail', array());
            $data     = json_decode($response, TRUE);
            tf()->assertEquals(EAPI_PARAM_USER_ID_NULL, $data['errno']);
        });


    }
 
}