<?php

class ModelDj {

	private $_ttl = 60;
	private $_cache_map;
	private $_key_prefix;

	public function cached($func, $args = array(), $ttl = null)
	{
		is_null($ttl) && ($ttl = $this->_ttl);

		if (!is_array($args)) {
			$args = array($args);
		}
		$key = $this->getKey($func, $args);

		if (!is_null($key) && Yaf_Application::app()->getConfig()->enableCache) {
			$redis = Yaf_Registry::get('redis');
        	$data = $redis->get($key);
            $data = unserialize($data);

			if (!$data) {
				$data = call_user_func_array(array($this, $func), $args);
				$data = serialize($data);
				$redis->setex($key, $ttl, $data);
			}
		} else {
			$data = call_user_func_array(array($this, $func), $args);
		}
		return $data;
	}

	public function getkey($func, $args = array())
	{
		$redisKey = Yaf_Registry::get("redis_config")->redisKey;
		$key_array[] = $redisKey;
		$key_array[] = $this->_key_prefix;
		$key_array[] = $this->_cache_map[$func];
		$key_array[] = $args[0];
        $key = implode(':', $key_array);
        return $key;
	}

}
