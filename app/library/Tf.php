<?php
class Tf{
	protected static $testify;
	
	public static function getTestify($coverage_path=null){
		if(null === static::$testify){
            require_once('Testify/Testify.php');
			static::$testify = new Testify('dianjing', $coverage_path);
		}
		return static::$testify;
	}
	
    /**
     * is not allowed to call from outside: private!
     *
     */
    private function __construct()
    {
    }

    /**
     * prevent the instance from being cloned
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized
     *
     * @return void
     */
    private function __wakeup()
    {
    }
}
