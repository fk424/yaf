<?php
/**
 * 配置信息
 */
return array(
	'prefix' => array(
    	'redisKey' => 'eapi',
		'djEgoodsRedisKey' => 'dj_e_goods',
		),
	'key' => array(
    	'user:detail' => 60,
    	'user:channel' => 60,
        'plan:infosByUserId' => 600,
    	'plan:info' => 600,
    	'plan:service' => 60,
    	'plan:area' => 60,
		)
);
