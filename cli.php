<?php
error_reporting(E_ALL);
define ("BASE_PATH", dirname(__FILE__).'/');
define ("APPLICATION_PATH", dirname(__FILE__) . "/app");


/* 默认的, Yaf_Application将会读取配置文件中在php.ini中设置的ap.environ的配置节 */
$application = new Yaf_Application("conf/main.ini");


$response = $application->bootstrap()->getDispatcher()->dispatch(new Yaf_Request_Simple());

?>
