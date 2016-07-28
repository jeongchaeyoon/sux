<?php

class BoardModel extends BaseModel {

	var $class_name = 'board_model';
	var	$board;
	var	$board_grg;
	var $id;

	var	$m_name;
	var	$pass;
	var	$storytitle;
	var	$storycomment;
	var	$email;
	var	$igroup;
	var	$name;
	var	$wall;
	var	$wallok;
	var	$wallwd;

	var	$imgup_name;
	var	$imgup_size;
	var	$imgup_type;
	var	$imgup_tmpname;

	function __construct() {

		parent::__construct();
	}

	function init() {

		$context = Context::getInstance();
		$requests = $context->getRequestAll();
		$posts = $context->getPostAll();
		$files = $context->getFiles();

		$this->board = $requests['board'];
		$this->board_grg = $this->board . '_grg';
		$this->id = $requests['id'];
		$this->grgid = $requests['grgid'];

		$this->m_name = $posts['m_name'];
		$this->pass = $posts['pass'];
		$this->storytitle = $posts['storytitle'];
		$this->storycomment = $posts['storycomment'];
		$this->email = $posts['email'];
		$this->igroup = $posts['igroup'];
		$this->name = $posts['type'];
		$this->wall = trim($posts['wall']);
		$this->wallok = trim($posts['wallok']);
		$this->wallwd = $posts['wallwd'];

		$this->imgup_name = $files['imgup']['name'];
		$this->imgup_size = $files['imgup']['size'];
		$this->imgup_type = $files['imgup']['type'];
		$this->imgup_tmpname = $files['imgup']['tmp_name'];
	}

	function SelectListFromBoardGroup() {

		$context = Context::getInstance();
		$group = $context->get('db_board_group');
		$field = '*';
		$where = array('name'=>$this->board);

		$query = new Query();
		if ($field != '') $query->setField($field);
		$query->setTable($group);
		if ($where != '') $query->setWhere($where);

		$result = parent::select($query);
		return $result;
	}

	function DeleteLimitwordFromBoard() {

		$row = $this->getRow();
		$limit_word = $row['limit_word'];
		if (isset($limit_word) && $limit_word != '') {

			$where = new QueryWhere();
			$limit_word_arr = split(',',$limit_word);
			for ($i=0; $i<count($limit_word_arr); $i++) {
				$limit_temp_str = trim($limit_word_arr[$i]);
				$where->set($row['limit_choice'], $limit_temp_str, 'like', 'or');
			}

			$query = new Query();
			$query->setTable($this->board);
			$query->setWhere($where);

			$result = parent::delete($query);
			return $result;			
		}		
	}

	function SelectFieldFromId($field) {

		$query = new Query();
		$query->setField($field);
		$query->setTable($this->board);
		$query->setWhere(array(
			'id' => $this->id
		));

		$result = parent::select($query);
		return $result;
	}

	function SelectFieldFromLimit($field ) {

		$query = new Query();
		$query->setField($field);
		$query->setTable($this->board);
		$query->setWhere(array(
			'space' => 0
		));
		$query->setOrderBy('id desc');
		$query->setLimit(1);
		$result = parent::select($query);
		return $result;
	}

	function SelectFieldFromCommentId($field) {

		$query = new Query();
		$query->setField($field);
		$query->setTable($this->board_grg);
		$query->setWhere(array(
			'id' => $this->grgid
		));
		$result = parent::select($query);
		return $result;
	}

	function InsertRecordWrite() {

		$this->SelectFieldFromLimit('id');
		$row = $this->getRow();
		$igroup = $row['id']+1; 

		$query = new Query();
		$query->setTable($this->board);
		$query->setColumn(array(
			'', 
			$this->m_name,
			$this->pass,
			$this->storytitle,
			$this->storycomment,
			$this->email,
			'now()',
			$_SERVER['REMOTE_ADDR'],
			0,
			'',
			$igroup,
			0,
			0,
			$this->wallwd,
			$this->imgup_name,
			$this->imgup_size,
			$this->imgup_type,
			$this->type
		));

		$result = parent::insert($query);
		return $result;
	}

	function InsertRecordReply() {

		$this->SelectFieldFromId('igroup, space, ssunseo');
		$row = $this->getRow();
		$igroup = $row['igroup']; 
		$space = $row['space']+1;
		$ssunseo = $row['ssunseo']+1;

		$query = new Query();
		$query->setTable($this->board);
		$query->setColumn(array(
			'', 
			$this->m_name, 
			$this->pass, 
			$this->storytitle, 
			$this->storycomment,
			$this->email, 
			'now()', 
			$_SERVER['REMOTE_ADDR'],
			0, 
			'', 
			$igroup, 
			$space, 
			$ssunseo, 
			$this->wallwd,
			$this->imgup_name, 
			$this->imgup_size, 
			$this->imgup_type, 
			$this->type
		));

		$result = parent::insert($query);
		return $result;
		
	}

	function UpdateRecordModify() {

		$query = new Query();
		$query->setTable($this->board);
		$query->setColumn(array(
			'name' => $this->m_name, 
			'title' => $this->storytitle, 
			'comment' => $this->storycomment,
			'email' => $this->email, 
			'filename' => $this->imgup_name, 
			'filesize' => $this->imgup_size, 
			'filetype' => $this->imgup_type, 
			'type' => $this->type
		));

		$query->setWhere(array(
			'id' => $this->id
		));

		$result = parent::update($query);
		return $result;
	}

	function DeleteRecordDelete() {

		$query = new Query();
		$query->setTable($this->board);
		$query->setWhere(array(
			'id'=>$this->id
		));

		$result = parent::delete($query);
		return $result;
	}

	function UpdatRrecordOpkey() {

		$context = Context::getInstance();
		$opkey = $context->getPost('opkey');

		$query = new Query();
		$query->setTable($this->board);
		$query->setColumn(array(
			'opkey'=>$opkey
		));
		$query->setWhere(array(
			'id'=>$this->id
		));
		$result = parent::update($query);
		return $result;
	}

	function InsertRecordWriteComment() {

		$context = Context::getInstance();
		$requests = $context->getRequestAll();
		$posts = $context->getPostAll();

		$board_grg = $requests['board_grg'];
		$id = $requests['id'];

		$ljs_name = $posts['ljs_name'];
		$ljs_pass = $posts['ljs_pass'];
		$comment = $posts['comment'];

		$query = new Query();
		$query->setTable($board_grg);
		$query->setColumn(array(
			'',
			$id,
			$ljs_name,
			$ljs_pass,
			$comment,
			'now()'
		));
		$result = parent::insert($query);
		return $result;
	}

	function DeleteRecordDeleteComment() {

		$context = Context::getInstance();
		$grgid = $context->getRequest('grgid');
		$board_grg = $context->getRequest('board_grg');	

		$query = new Query();
		$query->setTable($board_grg);
		$query->setWhere(array(
			'id'=>$grgid
		));
		$result = parent::delete($query);
		return $result;
	}
}
?>