<?php
class Eapi_ErrorHandler {

	public static function initHandler(){
		if(YAF_ENABLE_EXCEPTION_HANDLER)
			set_exception_handler(array('ErrorHandler','handleException'));
		if(YAF_ENABLE_ERROR_HANDLER) {
			set_error_handler(array('ErrorHandler','handleError'),error_reporting());
		}
	}

	public static function handleException($e)
	{
		// disable error capturing to avoid recursive errors
		restore_error_handler();
		restore_exception_handler();

		$exception_type = get_class($e);
		switch ($exception_type) {
			case "Eapi_Exception":
            	$intErrno = $e->getCode();
            	$strErrmsg = $e->getMessage();
            	$strErrdesc = $e->getDesc();
            	$arrFailMsg = array(
                	'code' => $intErrno,
                	'message' => $strErrmsg
            	);
            	if ($strErrdesc !== null) {
                	$arrFailMsg['description'] = $strErrdesc;
            	}
            	$arrRes = array(
                	'errno' => $intErrno,
                	'data' => array(
                    	'failures' => array(
                        	$arrFailMsg
                    	)
                	)
            	);
        		$ret = self::_output($arrRes);
				break;
			default:
				self::displayException($e);
				break;
		}
		$category = 'exception.' . get_class($e);
		$message = $e->__toString();
		if(isset($_SERVER['REQUEST_URI']))
			$message .= "\nREQUEST_URI=" . $_SERVER['REQUEST_URI'];
		if(isset($_SERVER['HTTP_REFERER']))
			$message .= "\nHTTP_REFERER=" . $_SERVER['HTTP_REFERER'];
		$message .= "\n---";
		$logger = new Log('/opt/www/code/eapi/yaf/tmp/_runtime', 'application.log');
		$logger->setLog($message);
		return $ret;

	}

	public static function handleError($code,$message,$file,$line)
	{
		if($code & error_reporting())
		{
			// disable error capturing to avoid recursive errors
			restore_error_handler();
			restore_exception_handler();

			$log = "$message ($file:$line)\nStack trace:\n";
			$trace = debug_backtrace();
			// skip the first 3 stacks as they do not tell the error position
			if(count($trace) > 3)
				$trace = array_slice($trace, 3);
			foreach($trace as $i => $t)
			{
				if(!isset($t['file']))
					$t['file'] = 'unknown';
				if(!isset($t['line']))
					$t['line'] = 0;
				if(!isset($t['function']))
					$t['function'] = 'unknown';
				$log .= "#$i {$t['file']}({$t['line']}): ";
				if(isset($t['object']) && is_object($t['object']))
					$log .= get_class($t['object']) . '->';
				$log .= "{$t['function']}()\n";
			}
			if(isset($_SERVER['REQUEST_URI']))
				$log .= 'REQUEST_URI='.$_SERVER['REQUEST_URI'];
			$logger = new Log('/opt/www/code/eapi/yaf/tmp/_runtime', 'application.log');
			$logger->setLog($log);

			self::displayError($code, $message, $file, $line);
		}
	}

	public static function displayError($code, $message, $file, $line)
	{
		if(YAF_DEBUG)
		{
			echo "<h1>PHP Error [$code]</h1>\n";
			echo "<p>$message ($file:$line)</p>\n";
			echo '<pre>';

			$trace = debug_backtrace();
			// skip the first 3 stacks as they do not tell the error position
			if(count($trace) > 3)
				$trace = array_slice($trace,3);
			foreach($trace as $i => $t)
			{
				if(!isset($t['file']))
					$t['file'] = 'unknown';
				if(!isset($t['line']))
					$t['line'] = 0;
				if(!isset($t['function']))
					$t['function'] = 'unknown';
				echo "#$i {$t['file']}({$t['line']}): ";
				if(isset($t['object']) && is_object($t['object']))
					echo get_class($t['object']) . '->';
				echo "{$t['function']}()\n";
			}

			echo '</pre>';
		}
		else
		{
			echo "<h1>PHP Error [$code]</h1>\n";
			echo "<p>$message</p>\n";
		}
	}

	/**
	 * Displays the uncaught PHP exception.
	 * This method displays the exception in HTML when there is
	 * no active error handler.
	 * @param Exception $exception the uncaught exception
	 */
	public static function displayException($exception)
	{
		if(YAF_DEBUG)
		{
			echo '<h1>' . get_class($exception) . "</h1>\n";
			echo '<p>' . $exception->getMessage() . ' (' . $exception->getFile() . ':' . $exception->getLine() . ')</p>';
			echo '<pre>' . $exception->getTraceAsString() . '</pre>';
		}
		else
		{
			echo '<h1>' . get_class($exception) . "</h1>\n";
			echo '<p>' . $exception->getMessage() . '</p>';
		}
	}

    private static function _output($data)
    {
        $data = json_encode($data);
		$method = Yaf_Dispatcher::getInstance()->getRequest()->getMethod();
		switch ($method) {
			case 'CLI':
        		$response = new Yaf_Response_Cli();
	        	$response->setBody($data);
        		$response->response();
				break;
			case 'TEST':
        		$response = new Yaf_Response_Cli();
	        	$response->setBody($data);
				break;
			default:
				header('Content-type: application/json;charset=utf-8');
        		$response = new Yaf_Response_Http();
       			$response->setBody($data);
        		$response->response();
 				break;
		}
		return $response;

    }


}
