<?php

class CRedis extends Singleton
{
    /**
     * class from php redis extension
     * @link https://github.com/ukko/phpredis-phpdoc
     * @var Redis
     */
    static private $_redis = array('master' => '', 'slave' =>'');

    public $host;
    /**
     * 如果访问redis时抛出Exception时，会尝试再次调用，直到$intTryTime次抛出异常，才返回失败
     * @var int
     */
    public $intTryTime = 3;
    public $intTryInterval = 100; //尝试间隔为100微妙

    public function __construct($obj = 'master')
    {
        $this->redis = new Redis();
        if (!($config = Yaf_Registry::get("redis_config"))) {
            $config = new Yaf_Config_Simple(include(CONFIG_PATH . '/redis.php'));
            Yaf_Registry::set("redis_config", $config);
        }
        if ($conf = $config->host) {
            if (!$this->connect($conf['host'], $conf['port'], $conf['timeout'])) {
                throw new Eapi_Exception(EAPI_REDIS_DISCONNECT);
            }
        }
        elseif ($obj == 'master') {
            $master = $config->master;
            if (!$this->connect($master['host'], $master['port'], $master['timeout'])) {
                throw new Eapi_Exception(EAPI_REDIS_DISCONNECT);
            }
        }
        else {
            $slave = $config->slave;
            if (!$this->connect($slave['host'], $slave['port'], $slave['timeout'])) {
                throw new Eapi_Exception(EAPI_REDIS_DISCONNECT);
            }
        }
        return $this->redis;
    }

    static public function getInstance($obj = 'master')
    {
        if (!(self::$_redis[$obj] instanceof CRedis)){
            $_instance = new CRedis;
            self::$_redis[$obj] = $_instance;
        }
        return self::$_redis[$obj];
    }

    private function connect($host, $port=6379, $timeout=false)
    {
        try {
            if ($host{0} == '/') {//unix domain socket
                return $this->redis->connect($host);
            }
            else {
                if ($timeout) {
                    return $this->redis->connect($host, $port, $timeout);
                }
                else {
                    return $this->redis->connect($host, $port);
                }
            }
        }
        catch(Exception $e) {
            return false;
        }
    }
    
    public function __call($strMethod, $arrParam)
    {
    	if (method_exists($this->redis, $strMethod)) {
    		return Utility::autoTryCall(array($this->redis, $strMethod), $arrParam, $this->intTryTime, $this->intTryInterval);
    	}
    	 
    	throw new Exception(get_class($this)."::{$strMethod} not defined");
    }
}


