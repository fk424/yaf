<?php

//Yii::import('application.controllers.t.libs.TController');

class ServiceTest
{
	public function testRedis(){
		tf()->test('Test Redis', function(){
//			$redis = Yii::app()->openApiMaster;
        	$key = 'itworks';
        	tf()->assertEquals($key, 'itworks', 'redis work');
		});
	}
}
?>
