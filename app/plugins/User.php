<?php
/**
 * Ap定义了如下的7个Hook,
 * 插件之间的执行顺序是先进先Call
 */
class UserPlugin extends Yaf_Plugin_Abstract {

	public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {

//		echo "Plugin routerStartup called <br/>\n";

//		echo "Request with base uir:" . $request->getBaseUri() . "<br/>\n";

//		echo "Request with request uri:" .$request->getRequestUri() . "<br/>\n";
	}

	public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
//		echo "Plugin routerShutdown called <br/>\n";
//		echo "Request routed result:" ;
//		print_r($request);
//		echo "<br/>\n";
//		echo "Functional route:" . Yaf_Dispatcher::getInstance()->getRouter()->getCurrentRoute();
//		echo "<br/>\n";
	}

	public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
		if (($request->getControllerName() != 'Index') && ($request->getActionName() != 'index')) {
	    	$view = new Eapi_JsonView();
    		Yaf_Dispatcher::getInstance()->setView($view);
    		Yaf_Registry::set('view','json');
		} else {
			Yaf_Dispatcher::getInstance()->autoRender(FALSE);
    		Yaf_Registry::set('view','simple');
		}

//		echo "Plugin DispatchLoopStartup called <br/>\n";
	}

	public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
//		print_r($request);
//		$params = $_GET + $_POST;
		$params = $request->getQuery() + $request->getPost();
		$params = $params + Yaf_Dispatcher::getInstance()->getRequest()->getParams();
		Yaf_Registry::set("params", $params);
//		echo "Plugin PreDispatch called <br/>\n";
	}

	public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
//		echo "Plugin postDispatch called <br/>\n";
	}

	public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
		switch (Yaf_Registry::get('view')) {
			case 'json':
				$method = $request->getMethod();
				switch ($method) {
					case 'CLI':
						break;
					case 'TEST':
						break;
					default:
						header('Content-type: application/json;charset=utf-8');
						break;
				}
				break;
			default:
				break;
		}

//		print_r($response);die;
//		echo "Plugin DispatchLoopShutdown called <br/>\n";
	}

}
