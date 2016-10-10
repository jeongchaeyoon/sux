<?
include "../../config/config.inc.php";

$context = Context::getInstance();
$context->init();

$action = $context->getRequest('action');
$index_url = $context->getServer('PHP_SELF');

/*$url = $context->getRequest('returnToURL');
echo 'returnToURL=' . $url;
echo ("<meta http-equiv='Refresh' content='0; URL=$url'>");
return;*/

$model = new PopupModel();
$controller = new PopupController($model);
$views = new PopupView($model, $controller);

if (isset($action) && $action) {
	$views->display($action);
} else {
	UIError::alertTo('파라미터 값을 확인해주세요.\n현재 페이지 메인으로 이동합니다.', $index_url . '?action=open');
}
?>