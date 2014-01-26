<?php
class ActionAction extends Yaf_Action_Abstract {
	function init() {
		$this->initView();
		echo "action init called<br/>\n";
	}

	function execute() {
		echo "Action::execute called11 <br/>\n";
	}

	function __destruct() {
		echo "action destruct called<br/>\n";
	}
}
?>
