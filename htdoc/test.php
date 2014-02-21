<?php
error_reporting(E_ALL);
define ("BASE_PATH", dirname(__FILE__) . "/../");
define ("APPLICATION_PATH", BASE_PATH . "app");
define ("CONFIG_PATH", BASE_PATH . "conf/");

define ("CASE_PATH", BASE_PATH . "tests");
define ("COVERAGE_PATH", BASE_PATH . "htdoc/coverage");

$blackDir = array(
	'/usr/lib/php/pear',
	CASE_PATH, 
	BASE_PATH . 'htdoc', 
	BASE_PATH . 'app/library/Testify',
	);

require_once 'File/Iterator/Autoload.php';
require_once 'PHP/CodeCoverage/Autoload.php';
require_once 'PHP/Timer/Autoload.php';
require_once 'Text/Template/Autoload.php';

require_once 'Mockery/Loader.php';

$loader = new \Mockery\Loader;
$loader->register();

$application = new Yaf_Application(BASE_PATH."conf/main.ini");


function tf(){
	return Tf::getTestify(COVERAGE_PATH);
}

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


class Test
{	
	protected $cases_path;
		
	/**
	 * 传入creative/CheckUrlAllow/okTest,creative/CheckUrlAllow/,xxx/xxx/xx的格式
	 * Enter description here ...
	 */
	public function run(){
		if (PHP_SAPI == 'cli') {
			$argv = $_SERVER['argv'];
			foreach ($argv as $arg) {
				$e = explode("=", $arg);
				if (count($e) == 2) {
					$_GET[$e[0]] = $e[1];
				} else {
					$_GET[$e[0]] = null;
				}
			}
		}

		$case = isset($_GET['case']) ?  $_GET['case'] : '/';
		$case = trim($case);
		$case_list = $this->getCaseList($case);
		foreach ($case_list as $class=>$methods){
			foreach ($methods as $m){
				$test_class = new $class('test');
				
				$test_class->$m();

			}
		}
	}
	
	/**
	 * 通过用户传入的$case=creative/CheckUrlAllow/okTest,xxx/xxx/xx来获取要执行的测试用例。
	 * 有几种情况：1.到最后就是某个目录；2.最后是某个文件 两种方式
	 * 最后返回 array('case class name', 'method');然后，下游就可以执行了
	 * 一期先弄单个目录的，其他的后面再弄  -- by libingye 20131031
	 * Enter description here ...
	 * @param unknown_type $case
	 */
	public function getCaseList($case){
		//先简单点，先判断是/的，然后,xx/xx/，然后,xx/xx/xx先只支持这几种
		$result = array();
		
		//遍历所有case
		if($case == '/'){
			if ($dh = opendir(CASE_PATH)){
				//第一级目录
				while (($file = readdir($dh)) !== false){
					if(is_dir(CASE_PATH."/".$file) && $file!="." && $file!=".." && $file != 'libs')
					{
						if( $chdir = opendir(CASE_PATH."/".$file."/")){
							//下面就是测试用例文件
							while(($f = readdir($chdir)) !== false){
								if($f != '.' && $f != '..' && !(strpos($f,'Testify.php')===false)){
									require_once CASE_PATH."/".$file."/".$f;
									$class_name = substr($f, 0, strpos($f, '.'));
									$methods = $this->getTestMethod($class_name);
									$result[$class_name] = $methods;
								}
							}
						}
					}
				}
			}
		}else{
			$paths = explode('/', $case);
			
			//xx/的情况
			if( count($paths) === 2 && empty($paths[1]) ){
				if( $chdir = opendir(CASE_PATH."/".$paths[0]."/")){
					//下面就是测试用例文件
					while(($f = readdir($chdir)) !== false){
						if($f != '.' && $f != '..' && !(strpos($f,'Testify.php')===false)){
							require_once CASE_PATH."/".$paths[0]."/".$f;
							$class_name = substr($f, 0, strpos($f, '.'));
							$methods = $this->getTestMethod($class_name);
							$result[$class_name] = $methods;
						}
					}
				}				
			}
			
			//xx/xx的情况
			if( count($paths) === 2 ){
				if(is_file(CASE_PATH.'/'.$case.'Testify.php')){
					require_once CASE_PATH.'/'.$case.'Test.php';
					$class_name = $paths[1].'Controller';
					$methods = $this->getTestMethod($class_name);
					$result[$class_name] = $methods;
				}
			}			
		}
		return $result;
	}
	
	/**
	 * 
	 * 获取某个测试类下面，所有的测试函数
	 * @param unknown_type $class_name
	 * @param unknown_type $method_name
	 */
	public function getTestMethod($class_name){
		$result = array();
		if( empty($method_name) || $method_name == '*') {
			$methods = get_class_methods($class_name);
			foreach ($methods as $m){
				if(substr($m, 0, 4) == 'test'){
					$result[] = $m;
				}
			}
			return $result;
		}else if(method_exists($class_name, $method_name)){
			return array($method_name);
		}
	}
}

$test = new Test();
$test->run();
$testify = tf();
$testify->endTest();

?>
