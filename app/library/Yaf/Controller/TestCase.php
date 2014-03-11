<?php
Class Yaf_Controller_TestCase extends PHPUnit_Framework_TestCase {
	static public function getController() {
		$class = get_called_class();
		return substr($class, 0, -4);
	}
	static public function getAction($method) {
		return substr(substr($method, 4), 0, -6);
	}
}
