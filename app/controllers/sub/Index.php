<?php
/**
 * 默认的控制器
 * 当然, 默认的控制器, 动作, 模块都是可用通过配置修改的
 * 也可以通过$dispater->setDefault*Name来修改
 */
class Sub_IndexController extends Yaf_Controller_Abstract {

	/**
	 * 如果定义了控制器的init的方法, 会在__construct以后被调用
	 */
	public function init() {
		$config = Yaf_Application::app()->getConfig();

		$this->getView()->assign("title", "Agile Platform Demo");
		$this->getView()->assign("webroot", $config->webroot);
	}

	public function indexAction() {
		echo "haha";
		$smarty = Yaf_Registry::get('smarty');
		$module = $this->getModuleName();
		unset($module);
		echo $smarty->render($this->getViewPath(). "/index.phtml");
	}
}
