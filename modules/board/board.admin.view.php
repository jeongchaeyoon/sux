<?php

class BoardAdminModule extends View
{
	
	var $class_name = 'board_admin_module';
	var $skin_path_list = array();
	var $session_data = null;
	var $request_data = null;
	var $post_data = null;
	var $document_data = array();

	function output() { 

		/**
		 * @class Template
		 * @brief Template is a Wrapper Class based on Smarty
		 */
		$__template = new Template();
		if (is_readable($this->skin_path_list['contents'])) {
			$__template->assign('copyrightPath', $this->copyright_path);
			$__template->assign('skinPathList', $this->skin_path_list);
			$__template->assign('sessionData', $this->session_data);
			$__template->assign('requestData', $this->request_data);
			$__template->assign('postData', $this->post_data);
			$__template->assign('documentData', $this->document_data);
			$__template->display( $this->skin_path_list['contents'] );	
		} else {
			echo '<p>스킨 파일경로를 확인하세요.</p>';
		}
	}
}

class BoardAdminView extends BoardAdminModule
{

	function displayBoardAdmin() {

		$this->displayList();
	}

	function displayList() {

		$context = Context::getInstance();
		$this->request_data = $context->getRequestAll();

		$this->document_data['jscode'] = 'list';
		$this->document_data['module_code'] = 'board';

		$rootPath = _SUX_ROOT_;
		$adminSkinPath = _SUX_PATH_ . "modules/admin/tpl";
		$skinPath = _SUX_PATH_ . "modules/board/tpl";

		$this->skin_path_list['root'] = $rootPath;
		$this->skin_path_list['dir'] = '';
		$this->skin_path_list['header'] = "{$adminSkinPath}/_header.tpl";
		$this->skin_path_list['contents'] = "{$skinPath}/admin_list.tpl";
		$this->skin_path_list['footer'] = "{$adminSkinPath}/_footer.tpl";

		$this->output();
	}

	function displayAdd() {

		$context = Context::getInstance();
		$this->request_data = $context->getRequestAll();

		$this->document_data['jscode'] = 'add';
		$this->document_data['module_code'] = 'board';

		$rootPath = _SUX_ROOT_;
		$adminSkinPath = _SUX_PATH_ . "modules/admin/tpl";
		$skinPath = _SUX_PATH_ . "modules/board/tpl";

		$this->skin_path_list['root'] = $rootPath;
		$this->skin_path_list['dir'] = '';
		$this->skin_path_list['header'] = "{$adminSkinPath}/_header.tpl";
		$this->skin_path_list['contents'] = "{$skinPath}/admin_add.tpl";
		$this->skin_path_list['footer'] = "{$adminSkinPath}/_footer.tpl";

		$this->output();
	}

	function displayModify() {

		$context = Context::getInstance();
		$id = $context->getParameter('id');		
		$this->request_data = $context->getRequestAll();

		$this->document_data['jscode'] = 'modify';
		$this->document_data['module_code'] = 'board';

		$rootPath = _SUX_ROOT_;
		$adminSkinPath = _SUX_PATH_ . "modules/admin/tpl";
		$skinPath = _SUX_PATH_ . "modules/board/tpl";

		$where = new QueryWhere();
		$where->set('id', $id);
		$this->model->selectFromBoardGroup('category, id', $where);

		$row = $this->model->getRow();
		foreach ($row as $key => $value) {
			$this->document_data[$key] = $value;
		}

		$skinDir = _SUX_PATH_ . "modules/board/skin/";
		$skinList = Utils::readDir($skinDir);
		if (!$skinList) {
			$msg = "스킨폴더가 존재하지 않습니다.";
			$resultYN = "N";
		}
		$this->document_data['skin_list'] = $skinList;

		$this->skin_path_list['root'] = $rootPath;
		$this->skin_path_list['dir'] = '';
		$this->skin_path_list['header'] = "{$adminSkinPath}/_header.tpl";
		$this->skin_path_list['contents'] = "{$skinPath}/admin_modify.tpl";
		$this->skin_path_list['footer'] = "{$adminSkinPath}/_footer.tpl";

		$this->output();
	}

	function displayDelete() {

		
		$context = Context::getInstance();
		$id = $context->getParameter('id');
		$this->request_data = $context->getRequestAll();

		$this->document_data['jscode'] = 'delete';
		$this->document_data['module_code'] = 'board';
		
		$where = new QueryWhere();
		$where->set('id', $id);
		$this->model->selectFromBoardGroup('id, category', $where);

		$row = $this->model->getRow();
		foreach ($row as $key => $value) {
			$this->document_data[$key] = $value;
		}

		$rootPath = _SUX_ROOT_;
		$adminSkinPath = _SUX_PATH_ . "modules/admin/tpl";
		$skinPath = _SUX_PATH_ . "modules/board/tpl";

		$this->skin_path_list['root'] = $rootPath;
		$this->skin_path_list['dir'] = '';
		$this->skin_path_list['header'] = "{$adminSkinPath}/_header.tpl";
		$this->skin_path_list['contents'] = "{$skinPath}/admin_delete.tpl";
		$this->skin_path_list['footer'] = "{$adminSkinPath}/_footer.tpl";
		
		$this->output();
	}

	function displaySkinJson() {

		$skinDir = _SUX_PATH_ . "modules/board/skin/";
		
		$msg = "";
		$resultYN = "Y";

		$skinList = Utils::readDir($skinDir);
		if (!$skinList) {
			$msg = "스킨폴더가 존재하지 않습니다.";
			$resultYN = "N";
		}
		
		sort($skinList);
		$data = array(	"data"=>array("list"=>$skinList),
						"result"=>$resultYN,
						"msg"=>$msg);

		$this->callback($data);
	}

	function displayListJson() {

		$dataObj = null;
		$dataList = array();
		$msg = "";
		$resultYN = "Y";

		$this->model->selectFromBoardGroup('*', null, 'id desc');
		$numrows = $this->model->getNumRows();
		if ($numrows > 0){

			$a = $numrows;
			$rows = $this->model->getRows();
			foreach ($rows as $key => $row) {

				$fields = array('no'=>$a);
				foreach ($row as $key => $value) {
					$fields[$key] = $value;
				}

				$dataList[] = $fields;
				$a--;
			}

			$dataObj = array("list"=>$dataList);
		} else {
			$msg = "게시판이 존재하지 않습니다.";
			$resultYN = "N";
		}
		//$msg = Tracer::getInstance()->getMessage();
		$data = array(	"data"=>$dataObj,
						"result"=>$resultYN,
						"msg"=>$msg);

		$this->callback($data);
	}

	function displayModifyJson() {

		$context = Context::getInstance();
		$id = $context->getPost('id');

		$dataObj = array();
		$msg = "";
		$resultYN = "Y";

		$where = new QueryWhere();
		$where->set('id', $id);
		$this->model->selectFromBoardGroup('*', $where);

		$numrows = $this->model->getNumRows();
		if ($numrows > 0) {
			$row = $this->model->getRow();
			foreach ($row as $key => $value) {
				$dataObj[$key] = $value;
			}
			$resultYN = "Y";
		} else {
			$resultYN = "N";
			$msg = '게시판이 존재하지 않습니다.';
		}
		$data = array(	"data"=>$dataObj,
						"result"=>$resultYN,
						"msg"=>$msg);

		$this->callback($data);
	}

	function displayCheckBoard() {

		$context = Context::getInstance();
		$category = $context->getPost('category');

		$dataObj	= "";
		$msg = "";
		$resultYN = "Y";

		$msg = "추가 생성 게시판 : ".$category."\n";

		if (!isset($category) || $category == '') {

			$msg = "카테고리명을 넣고 중복체크를 하십시오.";
			$resultYN = "N";

			$data = array(	"result"=>$resultYN,
							"msg"=>$msg);

			$this->callback($data);
			exit;
		}

		if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]{3,12}$/i', $category)) {

			$msg .= "카테고리명은 영문+숫자+특수문자('_')로 조합된 단어만 사용가능\n첫글자가 영문 또는 특수문자로 시작되는 4글자 이상 사용하세요.";

			$data = array(	"msg"=>$msg);			
			$this->callback($data);
			exit;
		} 

		$where = new QueryWhere();
		$where->set('category', $category);
		$this->model->selectFromBoardGroup('id', $where);

		$numrows = $this->model->getNumRows();
		if ($numrows> 0) {
			$msg = "${category}는 이미 존재하는 게시판입니다.";
			$resultYN = "N";
		} else {
			$msg = "${category}는 생성할 수 있는 게시판입니다.";
			$resultYN = "Y";
		}

		$data = array(	"result"=>$resultYN,
						"msg"=>$msg);

		$this->callback($data);
	}	
}
?>