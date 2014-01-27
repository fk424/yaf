<?php

class Utility
{

    public static function strlenAsGBK ($str)
    {
        $strGBK = mb_convert_encoding($str, "GBK", 'UTF-8');
        return strlen($strGBK);
    }

    /**
     * 把全角空格替换为半角空格，回车换行替换为半角空格，并去掉字符串的首尾空白字符
     * 
     * @param string $str            
     * @return string
     */
    public static function trim ($str)
    {
        static $bolInitEncoding = true;
        if ($bolInitEncoding) {
            mb_regex_encoding('UTF-8');
            mb_internal_encoding('UTF-8');
            $bolInitEncoding = false;
        }
        $str = mb_ereg_replace('　', ' ', $str);
        $str = mb_ereg_replace(chr(0xC2).chr(0xA0), ' ', $str);
        $str = str_replace(array(
            "\t",
            "\n",
            "\r"
        ), ' ', $str);
        $str = trim($str);
        return $str;
    }

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
                'CURL_INFO'     => Yii::app()->curl->info,
                'CURL_ERROR_CODE'   => Yii::app()->curl->error_code,
                'CURL_ERROR_STRING' => Yii::app()->curl->error_string,
            );
            ComAdLog::write(array(date('Y-m-d H:i:s'), $url, json_encode($data), json_encode($arr_log)), 'eapiErrorLog');
            Utility::sendMailToAudit('Eapi调用edc失败', $url.": logid：\ndata：".json_encode($data)."\n" . ' Curl Info : ' . json_encode($arr_log) . "\n");

        }
        
        return $res;
    }

    public static function auditApiPost($url, $data, $token=null)
    {
        if (! isset($token)) {
            $token = sha1(Yii::app()->params['edcApi']['sys_name'].'_'.Yii::app()->controller->id.'_'.Yii::app()->controller->action->id.uniqid());
        }

        $res = Yii::app()->curl->run(
            Yii::app()->params['auditApi']['url'] . $url . '?logid='.self::$logid,
            false,
            $data,
            $token
        );
        $res = json_decode($res, true);
        if (! isset($res['errno']))
            $res['errno'] = 1;
        return $res;
    }
    // $arrErrInfo是ComSearchAudit::check()返回值中的message字段
    public static function auditErrStr ($arrErrInfo, $strType)
    {
        if ($strType == 'keyword') {
            $arrNameMap = array(
                'punctuation' => DJAPI_EC_KEYWORD_INVALID_CHAR,
                'shangbiao' => DJAPI_EC_KEYWORD_TRADEMARK,
                'black' => DJAPI_EC_KEYWORD_BLACKWORD,
                'jingpin' => DJAPI_EC_KEYWORD_COMPETE_WORD
            );
        } else {
            $arrNameMap = array(
                'punctuation' => DJAPI_EC_CREATIVE_INVALID_CHAR,
                'shangbiao' => DJAPI_EC_CREATIVE_TRADEMARK,
                'black' => DJAPI_EC_CREATIVE_BLACKWORD,
                'jingpin' => DJAPI_EC_CREATIVE_COMPETE_WORD
            );
        }
        $arrRet = null;
        foreach ((array) $arrErrInfo as $strCate => $v) {
            foreach ($v as $strErrType => $v2) {
                if ($strCate != 'keywords') {
                    $arrRet[$strCate] = array(
                        'code' => $arrNameMap[$strErrType],
                        'blackword' => $v2['0'],
                        'message' => implode(', ', $v2)
                    );
                } else {
                    foreach ($v2 as $strWord => $v3) {
                        $arrRet[$strWord] = array(
                            'code' => $arrNameMap[$strErrType],
                            'message' => implode(', ', $v3)
                        );
                    }
                }
            }
        }
        return $arrRet;
    }

    /**
     * 验证url不能指向指定类型的文件，如exe、doc文件等
     */
    public static function validateUrlType ($strVal)
    {
        static $arrTargetUrlForbidType = array(
            'jpg' => true,
            'jpeg' => true,
            'gif' => true,
            'doc' => true,
            'docx' => true,
            'exe' => true,
            'mp3' => true,
            'swf' => true,
            'txt' => true,
            'pmb' => true,
            'avi' => true,
            'zip' => true,
            'rar' => true,
            'wav' => true
        );
        if (strncasecmp('http', $strVal, 4)) {
            $strUrl = 'http://' . $strVal;
        }
        $arrTmp = parse_url($strVal);
        if (empty($arrTmp['path'])) {
            return true;
        }
        $strExt = pathinfo($arrTmp['path'], PATHINFO_EXTENSION);
        if ($strExt && isset($arrTargetUrlForbidType[$strExt])) {
            return false;
        }
        return true;
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

    function cc_msubstr ($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
    {
        if (function_exists("mb_substr"))
            return mb_substr($str, $start, $length, $charset);
        elseif (function_exists('iconv_substr')) {
            return iconv_substr($str, $start, $length, $charset);
        }
        $re['utf-8'] = "/[/x01-/x7f]|[/xc2-/xdf][/x80-/xbf]|[/xe0-/xef][/x80-/xbf]{2}|[/xf0-/xff][/x80-/xbf]{3}/";
        $re['gb2312'] = "/[/x01-/x7f]|[/xb0-/xf7][/xa0-/xfe]/";
        $re['gbk'] = "/[/x01-/x7f]|[/x81-/xfe][/x40-/xfe]/";
        $re['big5'] = "/[/x01-/x7f]|[/x81-/xfe]([/x40-/x7e]|/xa1-/xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
        if ($suffix)
            return $slice . "…";
        return $slice;
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

    public static function urlXss($url)
    {
        if (preg_match('/\<\s*script|\<\s*iframe/i', $url)) {
            return true;
        }
        return false;
    }

    public static function checkUrl($weburl)
    {
        return preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/", $weburl);
    }

    /**
     * 写log
     * @param string $content log内容
     * @param string $file_name log文件名字
     * @return boole
     * @author jingguangwen@360.cn 2013-03-29
     */
    public static function writeLog($content, $file_name)
    {
        if (empty($content) || empty($file_name)) {
            return false;
        }
        $log_dir = Yii::app()->params['logDir'];
        //文件路径名
        $file = $log_dir . $file_name;
        //写内容
        ComAdLog::write($content, $file);
        return true;
    }

    /**
     * 同步数据到检索
     * AdBatchOperating::syncDataToSearch($content);
     * @param  array $content  = array(
     *    data => array(
     *        type    => ad_advert|ad_search_keywords,
     *        action  => add|update|delete,
     *    ),
     *    cmd  => redis_search
     * );
     * @param  int $id
     * @return bool
     */
    public static function syncDataToSearch($content, $logid, $from='') {
        if (empty($content)) {
            return false;
        }
        $exchange   = 'redis_search';
        if (!isset($content['cmd'])) {
            $content['cmd'] = $exchange;
        }
        $emq_data   = array(
            'msg_type'  => 'update',
            'msg_src'   => $exchange,
            'msg_id'    => '',
            'msg_time'  => time(),
            'content'   => $content,
        );
            CEmqPublisher::sendToSearch(
                Yii::app()->params['exchange']['redis_search'],
                json_encode($emq_data),
                Yii::app()->params['emq']
                );

        return;
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
        Yii::app()->curl->run($mailApi, false, $data);
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

    public static function ercPost($url, $data, $token=null)
    {
        $ercConfig = Yii::app()->params['ercConfig'];
        $timeStamp = microtime(true);
        $arr = array(
            'appToken' => $ercConfig['appToken'],
            'timeStamp' => $timeStamp,
            'ver' => $ercConfig['ver'],
        );
        foreach ($data as $key => $val) {
            $arr[$key] = $val;
        }
        $sign = md5(join('', $arr));
        unset($arr['appToken']);
        $arr['appKey'] = $ercConfig['appKey'];
        $arr['sign'] = $sign;

        $res = Yii::app()->curl->run($ercConfig['url'] . $url, false, $arr);
        $res = json_decode($res, true);
        return $res;
    }

    public static function checkUserSearchAdPermission($uid=0, $typeExt=0) {
        if($uid <= 0 || $typeExt <= 0) return false;
        $edcData = array(
            'userID' => $uid,
            'category' => $typeExt,
            'token' => 'ecommerce_SiteKey_'.$uid.'_'.$typeExt
        );
        $ret = self::edcApiPost('/ecommerce/SiteKey', $edcData);
        if(isset($ret['errno']) && $ret['errno'] == 0 && isset($ret['msg']['data']) && !empty($ret['msg']['data'])) {
            return true;
        }
        return false;
    }

     static $logid = '';
     /**
      ** logger id
      **
      ** @return string
      ** @author guichenglin@360.cn
      **/
    static function getLoggerID($moduleName)
    {
        if (self::$logid == '') {
            self::$logid = $moduleName.'_'.rand(1, 9) . microtime(true)*10000 . rand(100,999);
        }
        return self::$logid;
    }

    /*
     * 字符串是否包含通配符
     *
     * @param string
     * @return bool
     * @author wangzheng-sal@360.cn
     */
    public static function checkWildcard($subject)
    {
        $pattern = '/\{.*\}/';
        preg_match($pattern, $subject, $matches);
        if(empty($matches)) return true;

        return false;
    }

    /**
     * 发送邮件
     *
     * @param  string $mailGroup 邮件组
     * @param  string $title 标题
     * @param  string $table 内容
     *
     * @return bool
     */
    static public function sendMail($mailGroup, $title, $table)
    {
        if ($mailGroup == '' || $title == '' || $table == '') {
            return false;
        }

        $mailApi = 'http://10.108.68.121:888/notice/notice.php';

        $ip = `/sbin/ifconfig  | grep 'inet addr:'| grep -v '127.0.0.1' | cut -d: -f2 | awk 'NR==1 { print $1}'`;
        $data   = array(
            's' => $title."_".$ip,
            'c' => $table,
            'g' => $mailGroup
        );
        Yii::app()->curl->run($mailApi, false, $data);
        return true;
    }

    public static function cronLog($strMsg, $strLevel = CLogger::LEVEL_INFO) {
        if (defined('YII_CMD') && YII_CMD) {
            echo date('Y-m-d H:i:s') . "\t$strMsg\n";
        } else {
            Yii::log($strMsg, $strLevel);
        }
    }

    /**
     *
     * 将数据转换为csv行，和fputcsv类似，不过该函数以字符串形式返回结果
     * @param array|string $mixedData
     * @param string $strDelimiter
     * @param string $strEnclosure
     * @return string
     */
    public static function sputcsv($mixedData, $strDelimiter = ',', $strEnclosure = '"') {
        if (! is_array($mixedData)) {
            $mixedData = array(strval($mixedData));
        }
        foreach ($mixedData as &$v) {
            if (strpos($v, $strDelimiter) !== false || strpos($v, $strEnclosure) !== false) {
                $v = str_replace($strEnclosure, "{$strEnclosure}{$strEnclosure}", $v);
                $v = "{$strEnclosure}{$v}{$strEnclosure}";
            }
        }
        return implode($strDelimiter, $mixedData);
    }

    /**
     **
     ** 截取指定字节长度的字符串，非ASIIC字符算两个字节，如果最后是半个汉字则丢弃
     **
     ** @param string $str 字符串
     ** @param int $intLen 截取的长度，即字节数
     ** @return string 返回的新字符串
     **/
    public static function cutStrAsGBK($str, $intLen) {
        if (strlen($str) <= $intLen) {
            return $str;
        }
        $intTmp = (int) floor($intLen / 2);
        if (mb_strlen($str, 'UTF-8') <= $intTmp) {
            return $str;
        }
        $strGBK = mb_convert_encoding($str, "GBK", 'UTF-8');
        $strTmp = mb_strcut($strGBK, 0, $intLen, 'GBK');
        $strTmp = mb_convert_encoding($strTmp, 'UTF-8', 'GBK');

        return $strTmp;
    }

    public static function multiEdcPost(array $arr_data)
    {

        $result = array();
        foreach($arr_data as $key => &$val) {
            if (! isset($val['token'])) {
                $val['token'] = sha1(Yii::app()->params['edcApi']['sys_name'].'_'.Yii::app()->controller->id.'_'.Yii::app()->controller->action->id.uniqid());
            }
            if (! isset($val['source'])) {
                $val['source'] = Yii::app()->params['edcApi']['sys_name'].'_'.Yii::app()->controller->id.'_'.Yii::app()->controller->action->id;
            }
            $val['ip_address'] = isset($_SERVER['HTTP_CLIENTIP']) ? $_SERVER['HTTP_CLIENTIP'] : '';
            $val['opt_user_id'] = isset($_SERVER['HTTP_OPTUSERID']) ? $_SERVER['HTTP_OPTUSERID'] : 0;
            $val['data'] = self::makeClientSign($val['data']);
        }
        unset($val);

        $r = Yii::app()->curl->mutil_exec($arr_data);
        $logid = self::getLoggerID('MUTIL_EDC_POST');

        foreach($r as $key => $raw) {
            $res = json_decode($raw['data'], true);
            if (! isset($res['errno'])) {
                $res['errno'] = 1;
                $url = $arr_data[$key]['url'];
                $arr_log    = array(
                    'IP' => $_SERVER['REMOTE_ADDR'],
                    'CURL_INFO' => Yii::app()->curl->info,
                    'AUDIT_RESULT' => $raw['data'],
                    'CURL_ERROR_CODE' => Yii::app()->curl->error_code,
                    'CURL_ERROR_STRING' => Yii::app()->curl->error_string,
                );
                ComAdLog::write(array(date('Y-m-d H:i:s'), $url, json_encode($arr_data[$key]), json_encode($arr_log)), 'eapiErrorLog');
                Utility::sendMailToAudit('Eapi并发调用EDC失败', $url.": logid：$logid\ndata：".json_encode($arr_data[$key])."\n" . ' Curl Info : ' . json_encode($arr_log) . "\n");
            }
            $result[$key] = $res;
        }
        return $result;
    }

    public static function startProfile()
    {
        if (self::_is_load_model('xhprof'))
        {
            xhprof_enable();
        }
    }
    /**
     * 获取profile data
     */
    private static function _getProfileData()
    {
        if (self::_is_load_model('xhprof'))
        {
            $profile_data = xhprof_disable();
            return $profile_data;
        }
        echo "<br/>----@@-_-@@--------<br/>no xhprof module installed<br/>";
    }
    /**
     *
     * 展示profile信息
     * @param unknown_type $type
     */
    public static function renderProfile($type = 'open_360')
    {
        $xhprof_root     = __DIR__ . '/../../htdoc/xhprof';
        $xhprof_root_url = 'http://' .$_SERVER['HTTP_HOST'] .
            '/xhprof/xhprof_html';
        include_once $xhprof_root . "/xhprof_lib/utils/xhprof_lib.php";
        include_once $xhprof_root . "/xhprof_lib/utils/xhprof_runs.php";
        $profile_data = self::_getProfileData();
        $profile_obj  = new XHProfRuns_Default();
        $run_id       = $profile_obj->save_run($profile_data, $type);

        $xhprof_url = $xhprof_root_url . "/index.php?run=$run_id&source=$type";
        ComAdLog::write($xhprof_url, '/dev/shm/xhprof');
    }
    /**
     * 是否加载相关模块
     * @param string $model_name
     */
    private static function _is_load_model($model_name)
    {
        return get_extension_funcs($model_name);
    }

    /**
     * 根据$logid获取请求来源
     * @param string $logid
     */
    public static function mqMsgSource($logid='')
    {
        if (empty($logid)) return $logid;

        if (preg_match("/dianjing|openapi|eapi/i", $logid, $matches)) {
            $source = strtolower($matches[0]);
        } else {
            $source = "";
        }

        return $source;
    }
}
