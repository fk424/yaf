<?php
/**
 * 默认的控制器
 * 可以访问
 * http://yourdomain.com/subdirectory/st/action来验证默认查找Action的功能
 * 当然, 默认的控制器, 动作, 模块都是可用通过配置修改的
 * 也可以通过$dispater->setDefault*Name来修改
 */
class StController extends Yaf_Controller_Abstract {
	/**
	 * 如果定义了控制器的init的方法, 会在__construct以后被调用
	 */
	public function init() {
		$dao = new Dao_Name_Value();
		$service = new Service_Name_Value();
		echo "controller init called<br/>";
		$config = Yaf_Application::app()->getConfig();
		$this->getView()->assign("title", "Agile Platform Demo");
		$this->getView()->assign("webroot", $config->webroot);
	}

	public function indexAction() {
		$session = Yaf_Session::getInstance();
		if ($session->cout) {
			++$session->cout;
		} else {
			$session->cout = 1;
		}

		echo "Session Count : " . $session["cout"] . "<br/>";

		$this->getView()->assign("body", "Hello Wrold<br/>");
		$action = "Test";
		$this->forward("test", array("name" => "value"));
	}


	public function testAction() {
		/** 
		 * 关闭视图输出
		 */
		$this->getView()->assign("name", $this->getRequest()->getParam("name"));
		if ($this->getRequest()->isXmlHttpRequest()) {
			Yaf_Dispatcher::getInstance()->disableView();
		}
	}
}
