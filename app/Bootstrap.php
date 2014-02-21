<?php

/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Ap调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{

	public static $_logger;
	public function _initConst()
	{
		defined('YAF_ENABLE_EXCEPTION_HANDLER') or define('YAF_ENABLE_EXCEPTION_HANDLER',true);
		defined('YAF_ENABLE_ERROR_HANDLER') or define('YAF_ENABLE_ERROR_HANDLER',true);
		defined('YAF_DEBUG') or define('YAF_DEBUG',true);
		defined('YAF_TRACE_LEVEL') or define('YAF_TRACE_LEVEL',3);
		require_once(APPLICATION_PATH . '/library/Eapi/Errno.php');
	}

	public function _initConfig() {
		$config = Yaf_Application::app()->getConfig();
		Yaf_Registry::set("config", $config);
	}

	public function _initLibrary() {
	}

	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
		$user = new UserPlugin();
		$dispatcher->registerPlugin($user);
	}

	public function _initView(Yaf_Dispatcher $dispatcher) {
    }

}
