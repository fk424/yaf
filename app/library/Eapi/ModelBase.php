<?php

abstract class Eapi_ModelBase extends Singleton {

	protected $_ttl = 60;
//	protected $_cache_map;
//	protected $_key_prefix;

    static public function checkParams($params, $keys = null)
    {
        if (!is_null($keys))
        {
            foreach ($params as $k => $v)
            {
                if (!in_array($k, $keys))
                {
                    unset($params[$k]);
                }
            }
        }
        Eapi_Checker::assert_empty_array($params, EAPI_OPTIONAL_PARAM_NULL);
        foreach ($params as $k => $v)
        {
            switch ($k) {
                case 'userId':
                    Eapi_Checker::assert_int($v, EAPI_PARAM_USER_ID_INVALID);
                    break;
                case 'splitId':
                    Eapi_Checker::assert_int($v, EAPI_PARAM_SPLIT_ID_INVALID);
                    break;
                case 'planId':
                    Eapi_Checker::assert_int($v, EAPI_PARAM_PLAN_ID_INVALID);
                    break;
                case 'planIds':
                    Eapi_Checker::assert_json($v, EAPI_PARAM_PLAN_IDS_INVALID);
                    break;
                default:
                    break;
            }

        }
        return $params;

    }

	public function __call($name, $args)
	{
		$arr = explode('_', $name);
		$func = $arr[0];
		switch ($arr[1]) {
			case "cache":
				$ret = $this->cache($func, $args);
				break;
			case "batchcache":
				$ret = $this->batchcache($func, $args);
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
		if (!is_array($args)) {
			$args = array($args);
		}

		$key_ttl = $this->getKey($func, $args, 'cache');
		$key = $key_ttl[0];
		$ttl = isset($key_ttl[1]) ? $key_ttl[1] : $this->_ttl;

//		if (!($data = Yaf_Registry::get($key))){
			if (Yaf_Application::app()->getConfig()->enableCache) {
				$redis = CRedis::getinstance();
    	    	$data = $redis->get($key);
        	    $data = unserialize($data);

				if (!$data) {
					$data = call_user_func_array(array($this, $func), $args);
					$redis->setex($key, $ttl, serialize($data));
				}
			} else {
				$data = call_user_func_array(array($this, $func), $args);
			}
//			Yaf_Registry::set($key, $data);
//		}
		return $data;
	}


    public function batchcache($func, $args = array())
    {
    	$idList = $args[0];
        $cache_arr = array();
        $data_arr = array();
        $ids = array();
        if (Yaf_Application::app()->getConfig()->enableCache) {
            foreach ($idList as $id) {
                $key_ttl = $this->getKey($func, array($id), 'cache');
                $key = $key_ttl[0];
                $ttl = isset($key_ttl[1]) ? $key_ttl[1] : $this->_ttl;

                $redis = CRedis::getinstance();
                $cache = $redis->get($key);
                $cache = unserialize($cache);

                if (!$cache) {
                    $ids[] = $id;
                } else {
                    $cache_arr[] = $cache;
                }
            }
            if (count($ids) > 0) {
                $data_arr = call_user_func_array(array($this, $func), array($ids));
                foreach ($data_arr as $v) {
                    $redis->setex($key, $ttl, serialize($v));
                }
            }
            $data = Utility::array_sort(array_merge($cache_arr, $data_arr), 'id');
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
		if (Yaf_Application::app()->getConfig()->enableCache) {
			$keys = $this->getKey($func, $args, 'decache');
			$redis = CRedis::getinstance();

			$data = call_user_func_array(array($this, $func), $args);
			foreach ($keys as $key) {
				$redis->delete($key);
			}
		} else {
			$data = call_user_func_array(array($this, $func), $args);
		}
		return $data;
	}

	protected function getkey($func, $args = array(), $type = 'cache')
	{
		if (!($config = Yaf_Registry::get('cache_config'))) {
            $config = new Yaf_Config_Simple(include(CONFIG_PATH . '/cache.php'));
            Yaf_Registry::set("cache_config", $config);
		}
		$redisKey = $config->prefix->redisKey;
		if ($type == 'cache') {
			$key_array[] = $redisKey;
			$key_array[] = $this->_cache_func[$func];
			$key_array[] = $args[0];
			$key = implode(':', $key_array);
			$ttl = $config->key->$key_array[1];
        	return array($key, $ttl);
		} elseif ( $type == 'decache') {
			$dekeys = $this->_decache_func[$func];
			foreach ($dekeys as $v) {
				$key_array[] = $redisKey;
				$key_array[] = $v;
				$key_array[] = $args[0];
				$key = implode(':', $key_array);
				$keys[] = $key;
			}
	        return $keys;
    	}
	}

}
