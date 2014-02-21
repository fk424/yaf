<?php
error_reporting(E_ERROR);

require_once 'Mockery/Loader.php';

$loader = new \Mockery\Loader;
$loader->register();

define ("BASE_PATH", dirname(__FILE__).'/');
define ("APPLICATION_PATH", dirname(__FILE__) . "/app");


/* 默认的, Yaf_Application将会读取配置文件中在php.ini中设置的ap.environ的配置节 */
$application = new Yaf_Application("conf/main.ini");


//$dispatcher = $application->bootstrap()->getDispatcher();
//->dispatch(new Yaf_Request_Simple());

	function requestActionAndParseBody($controller, $action, $params=array()) {
        global $application;
        $request = new Yaf_Request_Simple("TEST", "Index", $controller, $action, $params);
        try {
            $response = $application->bootstrap()->getDispatcher()
                ->returnResponse(TRUE)
                ->dispatch($request);
        }
        catch (Exception $e) {
            $response = Eapi_ErrorHandler::handleException($e);
        }
        return $response->getBody();
    }

?>
