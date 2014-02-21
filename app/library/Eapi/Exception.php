<?php

class Eapi_Exception extends Exception
{

    protected $_strDesc;

    public function __construct ($errcode, $desc = null, $errmsg = null)
    {
        $this->_strDesc = $desc;
        if (empty($errmsg)) {
            $errmsg = Eapi_ErrorDescs::errmsg($errcode);
        }
        parent::__construct($errmsg, $errcode);
    }

    public function getDesc ()
    {
        return $this->_strDesc;
    }
}
