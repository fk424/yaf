<?php

/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Ap调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{
	public function _initSession($dispatcher) {
		/*
		 * start a session 
		 */
		Yaf_Session::getInstance()->start();
	}

	public function _initConfig() {
		$config = Yaf_Application::app()->getConfig();
		$conf_arr = $config->toArray();
		foreach ($config->include as $ini)
		{
			$config = new Yaf_Config_Ini(BASE_PATH.'conf/'.$ini.'.ini');
			$conf_arr[$ini] = $config->toArray();
		}
		$config = new Yaf_Config_Ini($conf_arr);
		Yaf_Registry::set("config", $config);
	}

	public function _initLibrary() {
		require_once(APPLICATION_PATH . '/library/DjApiErrorDescs.php');
	}

	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
		$user = new UserPlugin();
//		$dispatcher->registerPlugin($user);
	}
	public function _initRedis() {
		$redis = new CRedis();
		$redis->init();
		Yaf_Registry::set("redis", $redis->getInstance());
	}

	public function _initParams() {
		$params = $_GET + $_POST;
		Yaf_Registry::set("params", $params);
	}

	public function _initView(Yaf_Dispatcher $dispatcher) {
		$dispatcher->disableView();
        header('Content-type: application/json;charset=utf-8');
    }
}
