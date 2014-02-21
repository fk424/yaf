<?php

class Eapi_JsonView implements Yaf_View_Interface
{
    public $data = array();

    public function setScriptPath($path)
    {

    }
    public function getScriptPath()
    {

    }

    public function assign($spec, $value = null) {
        $this->data[$spec] = $value;
    }

    public function render($name, $value = NULL) {
        return json_encode($this->data);
    }

    public function display($name, $value = NULL) {
        echo render($name);
    }

}
?>