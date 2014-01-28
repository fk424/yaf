<?php

/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Ap调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{

	public function _initConst()
	{
		defined('YII_ENABLE_EXCEPTION_HANDLER') or define('YII_ENABLE_EXCEPTION_HANDLER',true);
		defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER',true);
		defined('YII_DEBUG') or define('YII_DEBUG',true);
	}
	protected function _initSystemHandlers()
	{
		if(YII_ENABLE_EXCEPTION_HANDLER)
			set_exception_handler(array($this,'handleException'));
		if(YII_ENABLE_ERROR_HANDLER)
			set_error_handler(array($this,'handleError'),error_reporting());
	}

	public function _initSession($dispatcher) {
		/*
		 * start a session 
		 */
		Yaf_Session::getInstance()->start();
	}

	public function _initConfig() {
		$config = Yaf_Application::app()->getConfig();
		$conf_arr = $config->toArray();
		foreach ($config->include as $ini)
		{
			$config = new Yaf_Config_Ini(BASE_PATH.'conf/'.$ini.'.ini');
			$conf_arr[$ini] = $config->toArray();
		}
		$config = new Yaf_Config_Ini($conf_arr);
		Yaf_Registry::set("config", $config);
	}

	public function _initLibrary() {
		require_once(APPLICATION_PATH . '/library/DjApiErrorDescs.php');
	}

	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
		$user = new UserPlugin();
//		$dispatcher->registerPlugin($user);
	}
	public function _initRedis() {
		$redis = new CRedis();
		$redis->init();
		Yaf_Registry::set("redis", $redis->getInstance());
	}

	public function _initParams() {
		$params = $_GET + $_POST;
		Yaf_Registry::set("params", $params);
	}

	public function _initView(Yaf_Dispatcher $dispatcher) {
    	$view= new JsonView();
    	Yaf_Dispatcher::getInstance()->setView($view);
    }

	public function handleException($exception)
	{
		// disable error capturing to avoid recursive errors
		restore_error_handler();
		restore_exception_handler();

		$category='exception.'.get_class($exception);
		if($exception instanceof CHttpException)
			$category.='.'.$exception->statusCode;
		// php <5.2 doesn't support string conversion auto-magically
		$message=$exception->__toString();
		if(isset($_SERVER['REQUEST_URI']))
			$message.="\nREQUEST_URI=".$_SERVER['REQUEST_URI'];
		if(isset($_SERVER['HTTP_REFERER']))
			$message.="\nHTTP_REFERER=".$_SERVER['HTTP_REFERER'];
		$message.="\n---";
//		Yii::log($message,CLogger::LEVEL_ERROR,$category);

		try
		{
			$event=new CExceptionEvent($this,$exception);
			$this->onException($event);
			if(!$event->handled)
			{
				// try an error handler
				if(($handler=$this->getErrorHandler())!==null)
					$handler->handle($event);
				else
					$this->displayException($exception);
			}
		}
		catch(Exception $e)
		{
			$this->displayException($e);
		}

		try
		{
			$this->end(1);
		}
		catch(Exception $e)
		{
			// use the most primitive way to log error
			$msg = get_class($e).': '.$e->getMessage().' ('.$e->getFile().':'.$e->getLine().")\n";
			$msg .= $e->getTraceAsString()."\n";
			$msg .= "Previous exception:\n";
			$msg .= get_class($exception).': '.$exception->getMessage().' ('.$exception->getFile().':'.$exception->getLine().")\n";
			$msg .= $exception->getTraceAsString()."\n";
			$msg .= '$_SERVER='.var_export($_SERVER,true);
			error_log($msg);
			exit(1);
		}
	}

	public function handleError($code,$message,$file,$line)
	{
		if($code & error_reporting())
		{
			// disable error capturing to avoid recursive errors
			restore_error_handler();
			restore_exception_handler();

			$log="$message ($file:$line)\nStack trace:\n";
			$trace=debug_backtrace();
			// skip the first 3 stacks as they do not tell the error position
			if(count($trace)>3)
				$trace=array_slice($trace,3);
			foreach($trace as $i=>$t)
			{
				if(!isset($t['file']))
					$t['file']='unknown';
				if(!isset($t['line']))
					$t['line']=0;
				if(!isset($t['function']))
					$t['function']='unknown';
				$log.="#$i {$t['file']}({$t['line']}): ";
				if(isset($t['object']) && is_object($t['object']))
					$log.=get_class($t['object']).'->';
				$log.="{$t['function']}()\n";
			}
			if(isset($_SERVER['REQUEST_URI']))
				$log.='REQUEST_URI='.$_SERVER['REQUEST_URI'];
			Yii::log($log,CLogger::LEVEL_ERROR,'php');

			try
			{
				Yii::import('CErrorEvent',true);
				$event=new CErrorEvent($this,$code,$message,$file,$line);
				$this->onError($event);
				if(!$event->handled)
				{
					// try an error handler
					if(($handler=$this->getErrorHandler())!==null)
						$handler->handle($event);
					else
						$this->displayError($code,$message,$file,$line);
				}
			}
			catch(Exception $e)
			{
				$this->displayException($e);
			}

			try
			{
				$this->end(1);
			}
			catch(Exception $e)
			{
				// use the most primitive way to log error
				$msg = get_class($e).': '.$e->getMessage().' ('.$e->getFile().':'.$e->getLine().")\n";
				$msg .= $e->getTraceAsString()."\n";
				$msg .= "Previous error:\n";
				$msg .= $log."\n";
				$msg .= '$_SERVER='.var_export($_SERVER,true);
				error_log($msg);
				exit(1);
			}
		}
	}

	public function onException($event)
	{
		$this->raiseEvent('onException',$event);
	}

	public function onError($event)
	{
		$this->raiseEvent('onError',$event);
	}

	public function raiseEvent($name,$event)
	{
		$name=strtolower($name);
		if(isset($this->_e[$name]))
		{
			foreach($this->_e[$name] as $handler)
			{
				if(is_string($handler))
					call_user_func($handler,$event);
				elseif(is_callable($handler,true))
				{
					if(is_array($handler))
					{
						// an array: 0 - object, 1 - method name
						list($object,$method)=$handler;
						if(is_string($object))	// static method call
							call_user_func($handler,$event);
						elseif(method_exists($object,$method))
							$object->$method($event);
						else
							throw new CException(Yii::t('yii','Event "{class}.{event}" is attached with an invalid handler "{handler}".',
								array('{class}'=>get_class($this), '{event}'=>$name, '{handler}'=>$handler[1])));
					}
					else // PHP 5.3: anonymous function
						call_user_func($handler,$event);
				}
				else
					throw new CException(Yii::t('yii','Event "{class}.{event}" is attached with an invalid handler "{handler}".',
						array('{class}'=>get_class($this), '{event}'=>$name, '{handler}'=>gettype($handler))));
				// stop further handling if param.handled is set true
				if(($event instanceof CEvent) && $event->handled)
					return;
			}
		}
		elseif(YII_DEBUG && !$this->hasEvent($name))
			throw new CException(Yii::t('yii','Event "{class}.{event}" is not defined.',
				array('{class}'=>get_class($this), '{event}'=>$name)));
	}


	public function hasEvent($name)
	{
		return !strncasecmp($name,'on',2) && method_exists($this,$name);
	}

	public function getErrorHandler()
	{
		return $this->getComponent('errorHandler');
	}

	public function getComponent($id,$createIfNull=true)
	{
		if(isset($this->_components[$id]))
			return $this->_components[$id];
		elseif(isset($this->_componentConfig[$id]) && $createIfNull)
		{
			$config=$this->_componentConfig[$id];
			if(!isset($config['enabled']) || $config['enabled'])
			{
				Yii::trace("Loading \"$id\" application component",'system.CModule');
				unset($config['enabled']);
				$component=Yii::createComponent($config);
				$component->init();
				return $this->_components[$id]=$component;
			}
		}
	}


	/**
	 * Displays the uncaught PHP exception.
	 * This method displays the exception in HTML when there is
	 * no active error handler.
	 * @param Exception $exception the uncaught exception
	 */
	public function displayException($exception)
	{
		if(YII_DEBUG)
		{
			echo '<h1>'.get_class($exception)."</h1>\n";
			echo '<p>'.$exception->getMessage().' ('.$exception->getFile().':'.$exception->getLine().')</p>';
			echo '<pre>'.$exception->getTraceAsString().'</pre>';
		}
		else
		{
			echo '<h1>'.get_class($exception)."</h1>\n";
			echo '<p>'.$exception->getMessage().'</p>';
		}
	}
}
