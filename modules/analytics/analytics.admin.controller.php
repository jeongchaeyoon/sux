<?php

class AnalyticsAdminController extends Controller
{

	function insertConnectSiteAdd() {

		$context = Context::getInstance();
		$keyword = $context->getPost('keyword');

		$msg = "";
		$resultYN = "Y";
		$returnURL = $context->getServer('REQUEST_URI');

		if (empty($keyword)) {
			$msg = '접속워드 이름을 입력하세요.';
			UIError::alertToBack($msg, true, array('url'=>$returnURL, 'delay'=>3));
			exit;
		}		

		$where = new QueryWhere();
		$where->set('name', $keyword);
		$this->model->selectFromConnectSite('id', $where);

		$numrow = $this->model->getNumRows();
		if ($numrow > 0) {
			$msg = "접속키워드가 이미 존재합니다.";
			$resultYN = "N";
		} else {

			$column = array('', $keyword, 0, 'now()');
			$result = $this->model->insertIntoConnectSite($column);
			if ($result) {
				$msg = "$keyword 접속키워드 추가를 성공하였습니다.";
				$resultYN = "Y";		 	
			} else {
				$msg = "$keyword 접속키워드 추가를 실패하였습니다.";
			 	$resultYN = "N";
			}			
		}	
		//$msg .= Tracer::getInstance()->getMessage();
		$data = array(	"result"=>$resultYN,
						"msg"=>$msg);

		$this->callback($data);
	}

	function updateConnectSiteReset() {
		
		$context = Context::getInstance();
		$id = $context->getPost('id');
		$keyword = $context->getPost('keyword');

		$msg = "";
		$resultYN = "Y";

		$column = array('hit_count'=>0);
		$where = new QueryWhere();
		$where->set('id', $id);
		$result = $this->model->updateConnectSite($column, $where);
		if ($result) {
			$msg = "$keyword 접속키워드 초기화를 성공하였습니다.";
			$resultYN = "Y";			
		} else {
			$msg = "$keyword 접속키워드 초기화를 실패하였습니다.";
			$resultYN = "N";
		}
		//$msg .= Tracer::getInstance()->getMessage();
		$data = array(	"result"=>$resultYN,
						"msg"=>$msg);

		$this->callback($data);
	}

	function deleteConnectSiteDelete() {
		
		$context = Context::getInstance();		
		$id = $context->getPost('id');
		$keyword = $context->getPost('keyword');

		$msg = "";
		$resultYN = "Y";

		$where = new QueryWhere();
		$where->set('id', $id);
		$result = $this->model->deleteFromConnectSite($where);
		if (!$result) {
			$msg = "$keyword 접속키워드 삭제를 실패하였습니다.";
			$resultYN = "N";
		} else {
			$msg = "$keyword 접속키워드 삭제를 성공하였습니다.";	
		}
		//$msg .= Tracer::getInstance()->getMessage();
		$data = array(	"result"=>$resultYN,
						"msg"=>$msg);

		$this->callback($data);
	}	

	function insertPageviewAdd() {

		$context = Context::getInstance();
		$keyword = $context->getPost('keyword');

		$msg = "";
		$resultYN = "Y";
		$returnURL = $context->getServer('REQUEST_URI');

		if (empty($keyword)) {
			$msg = '페이지 키워드 이름을 입력하세요.';
			UIError::alertToBack($msg, true, array('url'=>$returnURL, 'delay'=>3));
			exit;
		}	

		$where = new QueryWhere();
		$where->set('name', $keyword);
		$this->model->selectFromPageview('id',$where);

		$numrow = $this->model->getNumRows();
		if ($numrow > 0) {
			$msg = "페이지뷰 키워드가 이미 존재합니다.";
			$resultYN = "N";
		} else {

			$column = array('', $keyword, 0, 'now()');
			$result = $this->model->insertIntoPageview($column);
			if ($result) {
				$msg = "페이지뷰 키워드 추가를 성공하였습니다.";
				$resultYN = "Y";		 	
			} else {
				$msg = "페이지뷰 키워드 추가를 실패하였습니다.";
			 	$resultYN = "N";
			}
		}
		//$msg .= Tracer::getInstance()->getMessage();
		$data = array(	"result"=>$resultYN,
						"msg"=>$msg);

		$this->callback($data);
	}

	function updatePageviewReset() {
		
		$context = Context::getInstance();		
		$id = $context->getPost('id');
		$keyword = $context->getPost('keyword');

		$msg = "";
		$resultYN = "Y";

		$column = array('hit_count'=>0);

		$where = new QueryWhere();
		$where->set('id', $id);
		$result = $this->model->updatePageview($column, $where);
		if ($result) {
			$msg = "페이지뷰 초기화를 성공하였습니다.";
			$resultYN = "Y";
		} else {
			$msg = "페이지뷰 초기화를 실패하였습니다.";
			$resultYN = "N";			
		}
		//$msg .= Tracer::getInstance()->getMessage();
		$data = array(	"result"=>$resultYN,
						"msg"=>$msg);

		$this->callback($data);
	}

	function deletePageviewDelete() {
	
		$context = Context::getInstance();		
		$id = $context->getPost('id');
		$keyword = $context->getPost('keyword');

		$msg = "";
		$resultYN = "Y";

		$where = new QueryWhere();
		$where->set('id', $id);
		$result = $this->model->deleteFromPageview($where);
		if ($result) {
			$msg = "$keyword 페이지뷰 키워드 삭제를 성공하였습니다.";	
			$resultYN = "Y";			
		} else {
			$msg = "$keyword 페이지뷰 키워드 삭제를 실패하였습니다.";
			$resultYN = "N";
		}
		//$msg .= Tracer::getInstance()->getMessage();
		$data = array(	"result"=>$resultYN,
						"msg"=>$msg);

		$this->callback($data);	
	}
}
?>