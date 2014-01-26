<?php
class ActionAction extends Yaf_Action_Abstract {
	function init() {
		$this->initView();
		echo "action init called<br/>\n";
	}

	function execute() {
	}

	function __destruct() {
		echo "action destruct called<br/>\n";
	}
}
?>
