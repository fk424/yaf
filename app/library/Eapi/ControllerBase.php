<?php
/**
 * 默认的控制器
 * 当然, 默认的控制器, 动作, 模块都是可用通过配置修改的
 * 也可以通过$dispater->setDefault*Name来修改
 */
class Eapi_ControllerBase extends Yaf_Controller_Abstract {

    public function indexAction() {
        $methods = get_class_methods(get_class($this));
        foreach ($methods as $m) {
            if ((substr($m, -6) == 'Action') && ($m != 'indexAction')) {
                $actions[] = $m;
            }
        }
        $view = $this->getView();
        $view->assign("errno", EAPI_SUCCESS);
        $view->assign("data", $actions);
        $view->display(APPLICATION_PATH."/views/function.phtml");
    }

    /*
     *  @api
    */

}
