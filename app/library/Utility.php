<?php

class Utility
{
    public static function edcApiPost ($url, $data)
    {
        $config = Yaf_Registry::get('config');
        $request = Yaf_Dispatcher::getInstance()->getRequest();
        if (! isset($data['token'])) {
            $data['token'] = sha1($config->api->edc->sys_name.'_'.$request->getControllerName().'_'.$request->getActionName().uniqid());
        }
        if (! isset($data['source'])) {
            $data['source'] = $config->api->edc->sys_name.'_'.$request->getControllerName().'_'.$request->getActionName();
        }
        $data['ip_address'] = isset($_SERVER['HTTP_CLIENTIP']) ? $_SERVER['HTTP_CLIENTIP'] : '';
        $data['opt_user_id'] = isset($_SERVER['HTTP_OPTUSERID']) ? $_SERVER['HTTP_OPTUSERID'] : 0;
        $raw = Curl::run($config->api->edc->url . $url  . '?logid='.self::$logid, false, self::makeClientSign($data));
        $res = json_decode($raw, true);
        if (! isset($res['errno'])) {
            $res['errno'] = 1;
            
            $arr_log    = array(
                'AUDIT_RESULT'  => $raw, 
                'IP'            => $_SERVER['REMOTE_ADDR'],
                'CURL_INFO'     => Curl::$info,
                'CURL_ERROR_CODE'   => Curl::$error_code,
                'CURL_ERROR_STRING' => Curl::$error_string,
            );
            ComAdLog::write(array(date('Y-m-d H:i:s'), $url, json_encode($data), json_encode($arr_log)), 'eapiErrorLog');
            Utility::sendMailToAudit('Eapi调用edc失败', $url.": logid：\ndata：".json_encode($data)."\n" . ' Curl Info : ' . json_encode($arr_log) . "\n");

        }
        
        return $res;
    }

    
    // 在客户端生成sign值
    public static function makeClientSign ($params)
    {
        $config = Yaf_Registry::get('config');

        $edcConfig = $config->api->edc;
        $params['appkey'] = $edcConfig['appkey'];
        $params['ver'] = $edcConfig['ver'];
        $g_params = Yaf_Registry::get('params');
        $userId = isset($g_params['userId']) ? $g_params['userId'] : '';
        if (isset($params['forceToken'])) {
            $strToken = $params['forceToken'];
        } else {
            $strToken = $userId;
            $strToken .= implode($params);
            $strToken = md5($strToken);
        }
        $params['time_stamp'] = microtime(true);
        if (isset($_SERVER['HTTP_SERVETOKEN'])) {
            $strToken .= '_' . $_SERVER['HTTP_SERVETOKEN'];
        } else {
            $strToken .= '_';
        }
        if (isset($params['token'])) {
            $strToken .= '_' . $params['token'];
        }
        $params['token'] = sha1($strToken);

        $params['splitId'] =$userId;

        $res = array();
        foreach ($params as $k => $v) {
            if (! in_array($k, array(
                'sign'
            ))) {
                $res[trim($k)] = trim($v);
            }
        }
        $params['sign'] = md5(join('', $res) . $edcConfig['token']);
        return $params;
    }


    /**
     * 函数调用的代理，如果调用时有异常，会自动重试$intMaxTry次，每次间隔$intTryInterval微妙，如果
     * 重试指定次数后仍然异常，则抛出原异常
     *
     * @param callable $mixedCallable The callable to be called
     * @param array $arrParam The parameters to be passed to the callback, as an indexed array
     * @param int $intMaxTry 最大重试次数 每次重试的时间间隔
     * @return mixed
     */
    public static function autoTryCall ($mixedCallable, $arrParam, $intMaxTry = 2, $intTryInterval = 100)
    {
        if ($intMaxTry < 1)
            $intMaxTry = 1;
        if (is_array($mixedCallable)) {
            $strClassName = is_object($mixedCallable[0]) ? get_class($mixedCallable[0]) : strval($mixedCallable[0]);
            $strCallMethod = "{$strClassName}::{$mixedCallable[1]}";
        } else {
            $strCallMethod = strval($mixedCallable);
        }
        for ($i = 1; $i <= $intMaxTry; $i ++) {
            try {
                return call_user_func_array($mixedCallable, $arrParam);
            } catch (Exception $e) {
                Yii::log("{$strCallMethod} call failed({$i}) " . $e->getMessage(), CLogger::LEVEL_WARNING);
            }
            usleep($intTryInterval);
        }
        $strParam = serialize($arrParam);
        if (strlen($strParam) > 1024)
            $strParam = substr($strParam, 0, 1024);
        Yii::log("{$strCallMethod} call failed  " . $e->getMessage() . " param {$strParam}", CLogger::LEVEL_ERROR);
        throw $e;
    }


    /**
     * 发送邮件到审核
     *
     * @param  string $title 标题
     * @param  string $title 内容
     *
     * @return bool
     */
    static public function sendMailToAudit($title, $table)
    {    
        if ($title == '' || $table == '') {
            return false;
        }

        $mailApi        = 'http://10.108.68.121:888/notice/notice.php';

        $ip = `/sbin/ifconfig  | grep 'inet addr:'| grep -v '127.0.0.1' | cut -d: -f2 | awk 'NR==1 { print $1}'`;
        $data   = array(
            's' => $title."_".$ip,
            'c' => $table,
            'g' => 'eapi_monitor_mailonly'
        );
        Curl::run($mailApi, false, $data);
        return true;
    }

    /**
     * 异常提示函数
     *
     * @param  errno  $errno  错误编码
     * @param  string $msg    错误信息
     *
     * @return JSON
     */
    static public function CException($errno = 0, $description = '') 
    {
        if (YII_DEBUG) {
            throw new DjApiException($errno, $description);
        }

        $message = isset(DjApiErrorDescs::$_arrSysOpenApiError[$errno]) ? DjApiErrorDescs::$_arrSysOpenApiError[$errno] : '';
        $result = array(
            'errno' => $errno,
            'data'  => array(
                'failures' => array(
                    array(
                        'code'    => $errno,
                        'message' => $message
                    )   
                )   
            )   
        );  

        if ($description != '') {
            $result['data']['failures'][0]['description'] = $description;
        }

        echo json_encode($result);
        Yii::app()->end();
    }


     static $logid = '';
     /**
      ** logger id
      **
      ** @return string
      ** @author guichenglin@360.cn
      **/

}
