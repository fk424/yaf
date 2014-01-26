<?php

class ProductController extends Yaf_Controller_Abstract {

	/**
	 * 这里只是为了举例.
	 * 一般来说, 如果要使用Smarty, 那么就应该
	 * 在Bootstrap中初始化
	 */
	public function init() {
	}

	public function infoAction($name=NULL, $id = 0) {
		$smarty = new Smarty_Adapter(null, Yaf_Registry::get("config")->get("smarty"));
		Yaf_Dispatcher::getInstance()->autoRender(FALSE);
		$smarty->assign("body", "render by Smarty");
		$smarty->assign($this->getRequest()->getParams());
		echo $smarty->render(APPLICATION_PATH . "/views/product/info.phtml");
	}
}
