<?php
/* INI配置文件支持常量替换 */
define ("APPLICATION_PATH", dirname(__FILE__) . "/application");


/* 默认的, Yaf_Application将会读取配置文件中在php.ini中设置的ap.environ的配置节 */
$application = new Yaf_Application("conf/sample.ini");


$response = $application->bootstrap()->getDispatcher()->dispatch(new Yaf_Request_Simple());

?>
