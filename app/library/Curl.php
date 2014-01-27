<?php
/*

* Yii extension CURL

* This is a base class for procesing CURL REQUEST
*
* @author wangguoqiang@360.cn
* @version 0.2
* @modifid date: 2013-09-30
* @filesource CURL.php
*
*/

class Curl
{
    protected static $getCh, $postCh, $method;
    public static $options = array();
    public static $info = array(), $error_code, $error_string;



    protected $validOptions = array(
        'timeout'=>array('type'=>'integer'),
        'timeout_ms' => array('type' => 'integer'),
        'setOptions'=>array('type'=>'array'),
    );


    /**
     * Initialize the extension
     * check to see if CURL is enabled and the format used is a valid one
     */
    public function init(){
        if( !function_exists('curl_init') )
            throw new CException( Yii::t('Curl', 'You must have CURL enabled in order to use this extension.') );
        $this->checkOptions($this->options, $this->validOptions);
    }

    public function close()
    {
        if ($this->postCh) {
            curl_close($this->postCh);
        }
        if ($this->getCh) {
            curl_close($this->getCh);
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    protected static function checkOptions($value, $validOptions)
    {
        if (!empty($validOptions)) {
            foreach ($value as $key=>$val) {

                if (!array_key_exists($key, $validOptions)) {
                    throw new CException(Yii::t('Curl', '{k} is not a valid option', array('{k}'=>$key)));
                }
                $type = gettype($val);
                if ((!is_array($validOptions[$key]['type']) && ($type != $validOptions[$key]['type'])) || (is_array($validOptions[$key]['type']) && !in_array($type, $validOptions[$key]['type']))) {
                    throw new CException(Yii::t('Curl', '{k} must be of type {t}',
                        array('{k}'=>$key,'{t}'=>$validOptions[$key]['type'])));
                }

                if (($type == 'array') && array_key_exists('elements', $validOptions[$key])) {
                    self::checkOptions($val, $validOptions[$key]['elements']);
                }
            }
        }
    }

    /**
     * Setter
     * @set the option
     */
    protected static function setOption($ch, $key,$value){
        curl_setopt($ch,$key, $value);
    }

    public function setHeaders($headers)
    {
        $this->options['setOptions'] = array(CURLOPT_HTTPHEADER => $headers);
    }

    public static function get($url)
    {
        $this->method = 'get';
        if (!$this->getCh) {
            $this->getCh = curl_init();
            $this->defaults($this->getCh);
        }
        $this->setOption($this->getCh, CURLOPT_URL, $url);
        return $this->execute($this->getCh, $url);
    }

    public static function post($url, $postData)
    {
        self::$method = 'post';
        $index = 0;
        if (!self::$postCh) {
            self::$postCh = curl_init();
            self::defaults(self::$postCh);
        }
        self::setOption(self::$postCh, CURLOPT_URL, $url);
        self::setOption(self::$postCh, CURLOPT_POST, true);
        self::setOption(self::$postCh, CURLOPT_POSTFIELDS, self::buildQuery($postData));
        return self::execute(self::$postCh, $url);
    }

    protected static function execute($ch, $url)
    {
        if(isset(self::$options['setOptions'])) {
            foreach(self::$options['setOptions'] as $k=>$v) {
                self::$setOption($ch, $k, $v);
            }
        }
        $res = curl_exec($ch);
        if ($res === false) {
            self::$error_code = curl_errno($ch);
            self::$error_string = curl_error($ch);
            self::$info=array();
        }
        else {
            self::$info = curl_getinfo($ch);
            self::$error_code = self::$error_string = '';
        }
        $curlInfo = self::$info;
        $comebineData = array(
            date('YmdHis'),
            'curlLog',
            json_encode(array(
                $url,
                isset($curlInfo['http_code'])? $curlInfo['http_code']:self::$error_code,
                isset($curlInfo['total_time'])? $curlInfo['total_time']:self::$error_string,
                self::$method,
                $res)
            )
        );
        ComAdLog::combineLog($comebineData);

        return $res;
    }

    protected static function defaults($ch){
        isset(self::$options['timeout']) ?  self::setOption($ch, CURLOPT_TIMEOUT, self::$options['timeout']) : self::setOption($ch, CURLOPT_TIMEOUT, 30);
        isset(self::$options['setOptions'][CURLOPT_HEADER]) ? self::setOption($ch, CURLOPT_HEADER, self::$options['setOptions'][CURLOPT_HEADER]) : self::setOption($ch, CURLOPT_HEADER,FALSE);
        isset(self::$options['setOptions'][CURLOPT_RETURNTRANSFER]) ? self::setOption($ch, CURLOPT_RETURNTRANSFER,self::$options['setOptions'][CURLOPT_RETURNTRANSFER]) : self::setOption($ch, CURLOPT_RETURNTRANSFER,TRUE);
        isset(self::$options['setOptions'][CURLOPT_FOLLOWLOCATION]) ? self::setOption($ch, CURLOPT_FOLLOWLOCATIO,self::$options['setOptions'][CURLOPT_FOLLOWLOCATION]) : self::setOption($ch, CURLOPT_FOLLOWLOCATION,TRUE);
        isset(self::$options['setOptions'][CURLOPT_FAILONERROR]) ? self::setOption($ch, CURLOPT_FAILONERROR,self::$options['setOptions'][CURLOPT_FAILONERROR]) : self::setOption($ch, CURLOPT_FAILONERROR,TRUE);  
        if (isset(self::$options['timeout_ms'])) {
            $this->setOption($ch, CURLOPT_NOSIGNAL, 1);
            $this->setOption($ch, CURLOPT_TIMEOUT_MS, $this->options['timeout_ms']);
        }
    }

    /*
     * 兼容老代码  建议使用 get/post
     */
    public static function run($url, $method=true, $postData=array()){
        if( empty($url) )
            throw new CException( Yii::t('Curl', 'You must set Url.') );

        if( $method ){
            $res = self::get($url);
        }
        else {
            $res = self::post($url, $postData);
        }

        return $res;
    }

    protected static function buildQuery($postData)
    {
        return http_build_query($postData, null, '&');
    }

    /**
     * curl mutil exec
     *
     * @param array $resData    post data
     *
     * @return array
     * )
     */
    public function mutil_exec($resData)
    {
        $hArr = $xRow = array();
        foreach($resData as $k => $info) {
            $h = curl_init();
            curl_setopt($h, CURLOPT_URL, $info['url']);
            curl_setopt($h, CURLOPT_TIMEOUT, 30);
            curl_setopt($h, CURLOPT_HEADER, 0);
            curl_setopt($h, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($h, CURLOPT_POST, true);
            curl_setopt($h, CURLOPT_POSTFIELDS, $this->buildQuery($info['data']));
            $hArr[$k] = $h;
        }

        $mh = curl_multi_init();
        foreach($hArr as $k => $h) {
            curl_multi_add_handle($mh, $h);
        }

        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while($running > 0);

        foreach($hArr as $k => $h){
            $xRow[$k]['data'] = curl_multi_getcontent($h);
        }

        foreach($hArr as $k => $h){
            $info = curl_getinfo($h);
            curl_multi_remove_handle($mh,$h);
        }
        curl_multi_close($mh);
        return $xRow;
    }

}//end of method
