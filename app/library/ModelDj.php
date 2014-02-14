<?php

abstract class ModelDj {

	protected $_ttl = 60;
	protected $_cache_map;
	protected $_key_prefix;

    private function __construct(){

    }

    static public function getInstance() {
    	$class = get_called_class();
        if(!($class::$_model instanceof $class)){
            $class::$_model = new $class;
        }
        return $class::$_model;
    }

	public function __call($name, $args) 
	{
		$arr = explode('_', $name);
		$func = $arr[0];
		switch ($arr[1]) {
			case "cache":
				$ret = $this->cache($func, $args);
				break;
			case "decache":
				$ret = $this->decache($func, $args);
				break;
			default:
				throw new Yaf_Exception('model function: '.$name." not exist");
				break;
		}
		return $ret;
	}

	private function cache($func, $args = array())
	{
		$ttl = $this->_ttl;

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
				$redis->setex($key, $ttl, serialize($data));
			}
		} else {
			$data = call_user_func_array(array($this, $func), $args);
		}
		return $data;
	}

	private function decache($func, $args = array())
	{
		if (!is_array($args)) {
			$args = array($args);
		}
		$key = $this->getKey($func, $args);

		if (!is_null($key) && Yaf_Application::app()->getConfig()->enableCache) {
			$redis = Yaf_Registry::get('redis');

			$data = call_user_func_array(array($this, $func), $args);
			$redis->delete($key);
		} else {
			$data = call_user_func_array(array($this, $func), $args);
		}
		return $data;
	}

	private function getkey($func, $args = array())
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
