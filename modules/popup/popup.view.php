<?php

class PopupView extends BaseView {

	var $class_name = 'popup_view';

	// display function is defined in parent class 
}

class OpenerPanel extends BaseView {

	var $class_name = 'opener';

	function init() {

		$context = Context::getInstance();
		$requests = $context->getRequestAll();

		$skin_dir = _SUX_PATH_ . 'modules/popup/tpl/';

		$skin_path = $skin_dir . 'header.html';
		if (is_readable($skin_path)) {
			include $skin_path;
		} else {
			echo '헤더 파일경로를 확인하세요.<br>';
		}

		$result = $this->controller->select('fieldFromPopup', '*');
		if ($result) {

			$rows = $this->model->getRows();
			for($i=0; $i<count($rows); $i++) {
				$id = $rows[$i]['id'];				
				$name = $rows[$i]['popup_name'];
				$period = mktime($rows[$i]['time1'],$rows[$i]['time2'],$rows[$i]['time3'],$rows[$i]['time4'],$rows[$i]['time5'],$rows[$i]['time6']);
				$nowtime = mktime();
				$skin = $rows[$i]['skin'];
				$left = $rows[$i]['popup_left'];
				$top = $rows[$i]['popup_top'];
				$width = $rows[$i]['popup_width'];
				$height = $rows[$i]['popup_height'];
				$choice = 	$rows[$i]['choice'];
				$winname = $name;

				if ($choice == "y" && $nowtime < $period) {

					$url = 'popup.php?action=event&id=' . $id . '&winname=' . $winname . '&skin=' . $skin;

					echo 	'<script type=\'text/javascript\'>
								openPopup(\'' . $url . '\', \'' . $winname . '\', \'' . $left . '\', \'' . $top . '\', \'' . $width . '\', \'' . $height . '\');
							</script>';
				}
			}
		}
	} 
}

class EventPanel extends BaseView {

	var $class_name = 'event';

	function init() {

		$context = Context::getInstance();
		$requests = $context->getRequestAll();
		$popup_name = $requests['winname'];
		$skin_name = $requests['skin'];

		$skin_dir = _SUX_PATH_ . 'modules/popup/skin/' . $skin_name . '/';

		$skin_path = $skin_dir . 'index.html';
		if (is_readable($skin_path)) {
			include $skin_path;
		} else {
			echo '스킨 파일경로를 확인하세요.<br>';
		}
	}
}
?>