<?php

class DocumentAdminController extends Controller
{

	function insertAdd() { 

		$msg = '';
		$resultYN = 'Y';
		$dataObj = array();

		$context = Context::getInstance();
		$posts = $context->getPostAll();
		if (empty($posts)) {
			$posts = $context->getRequestToArray('document');
			$posts = $context->getJsonToArray($posts);
		}

		$category = $posts['category'];
		$title = $posts['document_name'];
		$returnURL = $context->getServer('REQUEST_URI');	

		$where = new QueryWhere();
		$where->set('category', $category);
		$this->model->select('document', 'id', $where);

		$numrows = $this->model->getNumRows();
		if ($numrows > 0) {
			$msg = $category . '페이지가 이미 등록되어 있습니다.';
			UIError::alertToBack($msg, true, array('url'=>$returnURL, 'delay'=>3));
			exit;
		} else {
			$this->model->select('board_group', 'id', $where);
			$numrows = $this->model->getNumRows();
			if ($numrows> 0) {
				$msg = "${category}는 게시판에서 이미 사용하고 있습니다.";
				UIError::alertToBack($msg, true, array('url'=>$returnURL, 'delay'=>3));
				exit;
			}
		}

		/**
		 * @cache's columns 
		 *  페이지에서 넘어온 데이터 값들은 캐시에 저장된 컬럼키와 매칭이 된 값만 저장된다.
		 */
		$cachePath = './files/caches/queries/document.getColumns.cache.php';
		$columnCaches = CacheFile::readFile($cachePath, 'columns');
		if (!$columnCaches) {
			$msg .= "QueryCacheFile Do Not Exists<br>";
		} else {
			$columns = array();
			for($i=0; $i<count($columnCaches); $i++) {
				$key = $columnCaches[$i];
				$value = $posts[$key];

				if (isset($value) && $value) {
					if (strpos($key,'contents_path') !== false) {
						if (!preg_match('/(.tpl+)$/i', $value)) {
							$value = $value . $category . '.tpl';						
						}
						$contents_path = $value;
					}
					$columns[] = $value;

					$msg .= $key . ' = '. $value . "\n";
				} else {					
					if ($key === 'date') {
						$columns[] = 'now()';
					} else if ($key === 'ip') {
						$columns[] = $_SEVER['REMOTE_ADDR'];
					}  else {
						$columns[] = '';
					}				
				}						
			}
		} // end of if

		$result = $this->model->insert('document', $columns);
		if ($result) {

			//  read and write contents
			if (isset($contents_path) && $contents_path) {
				$realPath = _SUX_PATH_ . 'files/document/';
				$filePath =Utils::convertAbsolutePath($contents_path, $realPath);

				$yoursite = $context->getAdminInfo('yourhome');
				$yoursite = strtoupper($yoursite);
				$buffHeader .=	 '{assign var=rootPath value=$skinPathList.root}' . "\n";
				$buffHeader .=	 '{assign var=headerPath value=$skinPathList.header}' . "\n";
				$buffHeader .=	 '{assign var=footerPath value=$skinPathList.footer}' . "\n";
				$buffHeader .=	 '{include file="$headerPath" title="' . $title . ' - ' . $yoursite . '"}' . "\n";
				$buffHeader .= '<!-- contents start -->' . "\n";

				$buff = $posts['contents'];
				if (empty($buff)) {
					$contentsPath = _SUX_PATH_ . 'modules/document/tpl/' . strtolower($category) . '.tpl';
					$buff = FileHandler::readFile($contentsPath);
					if (!$buff) {						
						$buff .= ''  . $category . ' 내용을 입력해주세요.';						
					}					
				} 
				$buffFooter .= '<!-- contents end -->' . "\n";
				$buffFooter .= '{include file="$footerPath"}';

				$buffers = $buffHeader . $buff . $buffFooter;
				$result = FileHandler::writeFile($filePath, $buffers);
				if (!$result) {
					$msg .= "${category} 템플릿 파일 등록을 실패하였습니다.<br>";
				} 
			}

			// write route's key
			$filePath = './files/caches/routes/document.cache.php';
			$routeCaches = CacheFile::readFile($filePath);			
			if (isset($routeCaches) && $routeCaches) {
				$routes['categories'] = $routeCaches['categories'];
				$routes['action'] = $routeCaches['action'];

				$pattern = sprintf('/(%s)+/i', $category);
				if (!preg_match($pattern, implode(',', $routes['categories']))) {
					$routes['categories'][] = $category; 
				}
				CacheFile::writeFile($filePath, $routes);
			}

			// insert into menu
			$columns = array();
			$columns[] = '';
			$columns[] = $posts['category'];
			$columns[] = $posts['document_name'];
			$columns[] = $posts['category'];
			$columns[] = 'now()';

			$result = $this->model->insert('menu', $columns);
			if (!$result) {
				$msg .= "메뉴 등록을 실패하였습니다.";
				$resultYN = 'N';
			}

			$where->reset();
			$where->set('category', $category);
			$result = $this->model->select('document', '*',  $where);
			if ($result) {
				$dataObj['list'] = $this->model->getRows();
			} else {
				$msg .= "${category} 페이지 선택을 실패하였습니다.<br>";
				$resultYN = 'N';
			}
		} else {
			$msg .= "${category} 페이지 등록을 실패하였습니다.<br>";
		}

		$msg = Tracer::getInstance()->getMessage();
		$data = array(	'data'=> $dataObj,
						'result'=>$resultYN,
						'msg'=>$msg);

		$this->callback($data);
	}

	function updateModify() {

		$dataObj = array();
		$resultYN = "Y";
		$msg = "";

		$context = Context::getInstance();
		$posts = $context->getPostAll();
		if (empty($posts)) {
			$posts = $context->getRequestToArray('document');
			$posts = $context->getJsonToArray($posts);
		}

		$id = $posts['id'];
		$category = $posts['category'];
		$contents_path = $posts['contents_path'];
		$contents = $posts['contents'];	

		$msg = $contents;
				
		/**
		 * @cache's columns 
		 *  페이지에서 넘어온 데이터 값은 캐시에 저장된 컬럼키와 매칭이 된 값만 저장된다.
		 */
		$cachePath = './files/caches/queries/document.getColumns.cache.php';
		$cacheColumns = CacheFile::readFile($cachePath);

		$columns = array();
		for($i=2; $i<count($cacheColumns['columns']); $i++) {
			$key = $cacheColumns['columns'][$i];
			$value = $posts[$key];

			if (isset($value) && $value) {
				if ($key === 'contents_path') {
					if (!preg_match('/(.tpl+)$/i', $value)) {
						$value = $value . $category . '.tpl';						
					}
					$contents_path = $value;
				}
				$columns[$key] = $value;
			} 					
		}
		// end of page

		$where = new QueryWhere();
		$where->set('id', $id);
		$result = $this->model->update('document', $columns, $where);		
		if ($result) {

			if (isset($contents_path) && $contents_path) {

				$realPath = _SUX_PATH_ . 'files/document/';
				$writeContentsTo =Utils::convertAbsolutePath($contents_path, $realPath);

				$buff = $posts['contents'];
				if (empty($buff)) {
					$readFromContents = _SUX_PATH_ . 'modules/document/tpl/' . $category . '.tpl';
					$buff = FileHandler::readFile($readFromContents);
					if (!$buff) {
						$buff = $category . ' 내용을 설정해주세요.';
					}
				}

				$result = FileHandler::writeFile($writeContentsTo, $buff);
				if (!$result) {
					$msg .= "$category 템플릿 파일 수정을 실패하였습니다.";
					$resultYN = "N";
				} else {
					$msg .= "$category 템플릿 파일 수정을 완료하였습니다.";
					$resultYN = "Y";
				}
			}

			$result = $this->model->select('document', '*', $where);
			if ($result) {
				$dataObj['list'] = $this->model->getRows();
			} 
		} else {
			$msg .= "$category 페이지 수정을 실패하였습니다.";
			$resultYN = "N";	
		}

		//$msg .= Tracer::getInstance()->getMessage();
		$data = array(	"data"=>$dataObj,
						"result"=>$resultYN,
						"msg"=>$msg);

		$this->callback($data);
	}

	function deleteDelete() {

		$context = Context::getInstance();
		$posts = $context->getPostAll();
		if (empty($posts)) {
			$posts = $context->getRequestToArray('document');
			$posts = $context->getJsonToArray($posts);
		}
		$category = $posts['category'];
		$id = $posts['id'];

		$resultYN = "Y";
		$msg = "";	

		$where = new QueryWhere();
		$where->set('id', $id);
		$this->model->select('document', 'contents_path', $where);
		$row = $this->model->getRow();		
			
		$result = $this->model->delete('document', $where);
		if (!$result) {
			$msg .= "${category} 페이지 삭제를 실패하였습니다.<br>";
		} else {
			$msg .= "${category} 페이지을 삭제하였습니다.<br>";

			$realPath = _SUX_PATH_ . 'files/document/';
			$contentsPath =Utils::convertAbsolutePath($row['contents_path'], $realPath);
			$result = FileHandler::deleteFile($contentsPath);
			if (!$result) {
				$msg .= "$category 컨텐츠파일 삭제를 실패하였습니다.<br>";
			}

			// 라우트 카테고리 키 저장 
			$filePath = './files/caches/routes/document.cache.php';
			$routes = CacheFile::readFile($filePath);
			$len = count($routes['categories']);
			for($i=0; $i<$len; $i++) {
				$input = $routes['categories'][$i];
				if (strcmp($input, $category) === 0) {
					array_splice($routes['categories'], $i, 1);
					break;
				}
			}

			$result = CacheFile::writeFile($filePath, $routes);
			if (!$sesult) {
				$msg .= "라우트 파일 재설정을 완료하였습니다.";
			}

			// delete menu
			$where = new QueryWhere();
			$where->set('category', $category);
			$result = $this->model->delete('menu', $where);
			if (!$result) {
				$msg .= "메뉴 삭제를 실패하였습니다.";
				$resultYN = 'N';
			}
		}
		//$msg .= Tracer::getInstance()->getMessage();
		$data = array(	"result"=>$resultYN,
						"msg"=>$msg);

		$this->callback($data);
	}
}