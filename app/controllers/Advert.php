<?php
/**
 * 默认的控制器
 * 当然, 默认的控制器, 动作, 模块都是可用通过配置修改的
 * 也可以通过$dispater->setDefault*Name来修改
 */
class AdvertController extends Eapi_ControllerBase {

    public $actions = array(
        "add" => "actions/Advert/add.php"
    );

    function init() {
//      $this->initView();
        echo "controller init called<br/>\n";
    }

    // public function addAction ()
    // {

    // }

    public function batchAddAction ()
    {

    }

    public function updateAction ()
    {

    }

    public function updateStatusAction ()
    {

    }

    public function updateByIdsAction ()
    {

    }

    public function updateStatusByIdsAction ()
    {

    }

    public function deleteByIdsAction ()
    {

    }


    public function getInfoAction ()
    {

    }

    /*
     * todo: getStatusByIdList, changedIdList
     */
    public function getInfosByIdsAction ()
    {

    }

    public function getAuditInfosByIdsAction ()
    {

    }

    public function getInfosByGroupIdAction ()
    {

    }

    public function getNumByGroupIdAction ()
    {

    }

    public function auditByIdsAction ()
    {

    }

}
