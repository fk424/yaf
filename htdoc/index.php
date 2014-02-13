<?php
/* INI配置文件支持常量替换 */
error_reporting(E_ALL);
define ("BASE_PATH", dirname(__FILE__) . "/../");
define ("APPLICATION_PATH", BASE_PATH . "app");
define ("CONFIG_PATH", BASE_PATH . "conf/");
/**
 * 默认的, Yaf_Application将会读取配置文件中在php.ini中设置的ap.environ的配置节
 * 另外在配置文件中, 可以替换PHP的常量, 比如此处的APPLICATION_PATH
 */
$main = include(CONFIG_PATH . "main.php");
$application = new Yaf_Application($main);
//Yaf_Dispatcher::getInstance()->throwException(FALSE);
/* 如果打开flushIstantly, 则视图渲染结果会直接发送给请求端
 * 而不会写入Response对象
 */
//$application->getDispatcher()->flushInstantly(TRUE);

/* 如果没有关闭自动response(通过Yaf_Dispatcher::getInstance()->returnResponse(TRUE)), 
 * 则$response会被自动输出, 此处也不需要再次输出Response
 */
$benchmark = new benchmark();

try {
	$response = $application
		->bootstrap() /*bootstrap是可选的调用*/
		->run()/*执行*/;	
}
catch (Exception $e) {
	$response = ErrorHandler::handleException($e);
}
echo "\ncost time:", $benchmark->cost(), 'ms';
echo "\nmemory:", $benchmark->memory(), 'byte';
echo "\npeak memory:", $benchmark->peak_memory(), 'byte';
?>
