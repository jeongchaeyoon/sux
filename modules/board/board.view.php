<?php
class BoardView extends View
{
  function displayList() {
    
    $context = Context::getInstance();
    $UIError = UIError::getInstance();

    $returnURL = $context->getServer('REQUEST_URI');
    $request_data = $context->getRequestAll();
    $session_data = $context->getSessionAll();
    
    $category = $context->getParameter('category');
    $passover = (int) $request_data['passover'];
    
    $find = $request_data['find'];
    $search = $request_data['search'];
    
    if (empty($passover)) {
       $passover = 0;
    }
    
    $this->document_data['jscode'] = 'list';
    $this->document_data['module_code'] = 'board';
    $this->document_data['module_name'] = '게시판 목록';     

    $where = new QueryWhere();
    $where->set('category',$category,'=');
    $this->model->select('board_group', '*', $where);

    $groupData = $this->model->getRow();
    $headerPath = $groupData['header_path'];
    $skinName = $groupData['skin_path'];
    $footerPath = $groupData['footer_path'];
    $limit = $groupData['limit_pagination'];

    /**
     * css, js file path handler
     */
    $rootPath = _SUX_ROOT_;
    $skinPath = _SUX_ROOT_ . "modules/board/skin/${skinName}/";
    $skinRealPath = _SUX_PATH_ . "modules/board/skin/${skinName}/";    

    /**
     * @var headerPath
     * @descripttion
     * smarty include 상대경로 접근 방식이 달라서 convertAbsolutePath()함수에 절대경로 처리 함.
     */   
    $headerPath = Utils::convertAbsolutePath($headerPath, _SUX_PATH_);
    $footerPath = Utils::convertAbsolutePath($footerPath, _SUX_PATH_);

    if (!is_readable($headerPath)) {
      $headerPath = "{$skinRealPath}/_header.tpl";
      $UIError->add("상단 파일경로가 올바르지 않습니다.");
    }

    if (!is_readable($footerPath)) {
      $footerPath = "{$skinRealPath}/_footer.tpl";
      $UIError->add("하단 파일경로가 올바르지 않습니다.");
    }

    $where = new QueryWhere();    
    if (isset($search) && $search) {
      $where->set($find, $search, 'like');
    }

    // total rows from board
    $where->set('category', $category, '=');
    $result = $this->model->select('board', '*', $where);
    if ($result) {

      // The value of numrows use in order to navi
      $numrows = $this->model->getNumRows();

      // limit rows from board
      $where->reset();
      if (isset($search) && $search) {
        $where->set($find, $search, 'like');
      }
      $where->set('category', $category, '=');
      $result = $this->model->select('board', '*', $where, 'igroup_count desc, ssunseo_count asc', $passover, $limit);
      if ($result) {
        $numrows2 = $this->model->getNumRows();
        $contentData['list'] = $this->model->getRows();         
        $today = date("Y-m-d");

        for ($i=0; $i<count($contentData['list']); $i++) {

          $id = (int) $contentData['list'][$i]['id'];
          $user_id = FormSecurity::decodeByIdentity($contentData['list'][$i]['user_id']);          
          $name = FormSecurity::decodeByIdentity($contentData['list'][$i]['user_name']); 
          $title = FormSecurity::decodeBySimpleTags($contentData['list'][$i]['title']);
          $contents = FormSecurity::decodeByText($contentData['list'][$i]['contents']);
          $progressStep = FormSecurity::decodeByIdentity($contentData['list'][$i]['progress_step']);
          $hit = (int) $contentData['list'][$i]['readed_count'];
          $space = (int) $contentData['list'][$i]['space_count'];
          $filename = $contentData['list'][$i]['filename'];
          $filetype = $contentData['list'][$i]['filetype'];
          
          $date =$contentData['list'][$i]['date'];        
          $compareDayArr = split(' ', $date);
          $compareDay = $compareDayArr[0];
          
          if (isset($search) && $search != '') {

            $search_replace = sprintf('<span class="sx-text-success">%s</span>', $search);
            $find_key = strtolower($find);
            switch ($find_key) {
              case 'title':
                $title = str_replace($search,$search_replace,$title);
                break;
              case 'name':
                $name = str_replace($search,$search_replace,$name);
                break;
              default:
                break;
            }
          }

          $subject = array();
          $subject['id'] = $id;
          $subject['title'] = $title;         
          $subject['icon_img_name'] = '';
          $subject['progress_step_name'] = '';

          // 'hide' in value is a class name of CSS
          $subject['space'] = 0;
          $subject['prefix_icon'] = '';
          $subject['prefix_icon_type'] = 0;

          $subject['icon_img'] = 'sx-hide';
          $subject['comment_num'] = '';
          $subject['icon_new'] = 'sx-hide';
          $subject['icon_opkey'] = 'sx-hide';

          if (isset($space) && $space) {
            $subject['space'] = $space*10;
            $subject['prefix_icon'] = '답변';
            $subject['prefix_icon_color'] = 'sx-bg-replay';
          }

          //공지글 설정은 개발 예정 
          /*if (isset($isNotice) && $isNotice != '') {
            $subject['space'] = '10px';
            $subject['prefix_icon'] = '공지';
            $subject['prefix_icon_color'] = 'sx-bg-notice';
          }*/

          if (isset($filename) && $filename){
            if (preg_match('/(image\/gif|image\/jpeg|image\/x-png|image\/bmp)+/', $filetype)) {             
              $imgname = "icon_img.png";
            } else if ($download === 'y'  && preg_match('/(application/x-zip-compressed|application/zip)+/', $filetype)) { 
              $imgname = "icon_down.png";
            }

            if (isset($imgname) && $imgname) {
              $subject['icon_img'] = 'sx-show-inline';
              $subject['icon_img_name'] = $imgname;
            } 
          }

          $where->reset();
          $where->set('contents_id', $id, '=');
          $this->model->select('comment', 'id', $where);
          $commentNums = $this->model->getNumRows();
          if ($commentNums > 0) {
            $subject['comment_num'] = $commentNums;
          }

          if ($compareDay == $today){
            $subject['icon_new'] = 'sx-show-inline';
            $subject['icon_new_title'] = 'new';
          }
          
          $subject['progress_step_name'] = ($progressStep === '초기화') ? '' : $progressStep;
          $subject['icon_progress_color'] = 'sx-bg-progress';

          $contentData['list'][$i]['name'] = $name;
          $contentData['list'][$i]['hit'] = $hit;
          $contentData['list'][$i]['space'] = $space;
          $dateArr = split(' ', $date);
          $contentData['list'][$i]['date'] = $dateArr[0];
          $contentData['list'][$i]['subject'] = $subject;

          $subject = null;
        }
      } else {
        $UIError->add('게시물 목록 가져오기를 실패하였습니다.');
      }
    } else {
      $UIError->add('게시물 전체 목록 가져오기를 실패하였습니다.');
    }

    // navi logic
    $navi = New Navigator();
    $navi->passover = $passover;
    $navi->limit = $limit;
    $navi->total = $numrows;
    $navi->init();

    $this->request_data = $request_data;
    $this->session_data = $sessionData;
    $this->document_data['pagination'] = $navi->get();
    $this->document_data['group'] = $groupData;
    $this->document_data['contents'] = $contentData;
    $this->document_data['category'] = $category;
    
    $this->skin_path_list['root'] = $rootPath;
    $this->skin_path_list['path'] = $skinPath;
    $this->skin_path_list['realPath'] = $skinRealPath;
    $this->skin_path_list['header'] = $headerPath;
    $this->skin_path_list['contents'] = "{$skinRealPath}/list.tpl";
    $this->skin_path_list['footer'] = $footerPath;

    $this->output();
  } 

  function displayRead() {

    $UIError = UIError::getInstance();
    $context = Context::getInstance();

    $this->request_data = $context->getRequestAll();
    $this->session_data = $context->getSessionAll();

    $category = $context->getParameter('category');
    $id = $context->getParameter('id');
    $this->document_data['category'] = $category;
    $this->document_data['id'] = $id;

    $this->document_data['jscode'] = 'read';
    $this->document_data['module_code'] = 'board';
    $this->document_data['module_name'] = '게시판 읽기';

    $find = $this->request_data['find'];
    $search = $this->request_data['search'];

    $returnURL = $context->getServer('HTTP_REFERER');
    $returnURL = urldecode($returnURL);   
    if (isset($search) && $search) {
      $returnURL .= "?find=${find}&search=${search}";
    }

    $grade = $this->session_data['grade'];
    $user_name = $this->session_data['user_name'];
    $password = $this->session_data['password'];

    $PHP_SELF = $context->getServer("PHP_SELF");  

    $where = new QueryWhere();
    $where->set('category',$category,'=');
    $this->model->select('board_group', '*', $where);

    $groupData = $this->model->getRow();
    $nonmember = strtolower($groupData['allow_nonmember']);
    $grade_r = strtolower($groupData['grade_r']);
    $is_readable = strtolower($groupData['is_readable']);
    $is_download = strtolower($groupData['is_download']);
    $is_comment = strtolower($groupData['is_comment']);
    $is_progress_step = strtolower($groupData['is_progress_step']);
    $headerPath = $groupData['header_path'];
    $skinName = $groupData['skin_path'];
    $footerPath = $groupData['footer_path'];
    $contentsType = $groupData['board_type'];

    /**
     * css, js file path handler
     */
    $rootPath = _SUX_ROOT_;
    $skinPath = _SUX_ROOT_ . "modules/board/skin/${skinName}/";
    $skinRealPath = _SUX_PATH_ . "modules/board/skin/${skinName}/";   

    $headerPath =Utils::convertAbsolutePath($headerPath, _SUX_PATH_);
    $footerPath = Utils::convertAbsolutePath($footerPath, _SUX_PATH_);

    if (!is_readable($headerPath)) {
      $headerPath = "{$skinRealPath}/_header.tpl";
      $UIError->add('상단 파일경로가 올바르지 않습니다.');
    }

    if (!is_readable($footerPath)) {
      $footerPath = "{$skinRealPath}/_footer.tpl";
      $UIError->add('하단 파일경로가 올바르지 않습니다.');
    }

    if (isset($grade) && $grade) {
      $level = $grade;
    } else {
      $level = 0;
    }

    // level
    if ($level < $grade_r) {
      $msg .= '죄송합니다. 읽기 권한이 없습니다.';
      UIError::alertTo($msg, true, array('url'=>$returnURL, 'delay'=>3));
      exit;
    }

    // nonmember's authority
    if ($nonmember != 'y') {
      if (empty($user_name)) {
        $returnToURL = $rootPath . $category . '/'. $id ;
        $msg = '죄송합니다. 이곳은 회원 전용 게시판 입니다.<br>로그인을 먼저 하세요.';
        UIError::alertTo( $msg, true, array('url'=>$rootPath . 'login?return_url=' . $returnToURL, 'delay'=>3));
        exit;
      } 
    }

    // admin
    if ($is_readable == 'n') {
      if ($context->checkAdminPass() === FALSE) {
        $msg = '죄송합니다. 이곳은 관리자 전용 게시판입니다.';
        UIError::alertTo( $msg, true, array('url'=>$returnURL, 'delay'=>3));
        exit;
      }
    }

    // read panel
    $where->reset();
    $where->set('id',$id,'=');
    $this->model->select('board', 'readed_count', $where);

    $row = $this->model->getRow();
    $hit = $row['readed_count']+1;
    $this->model->update('board', array('readed_count'=>$hit), $where);
    $this->model->select('board','*', $where);
    $contentData = $this->model->getRow();
    $contentData['user_name'] = FormSecurity::decodeByText($contentData['user_name']);
    $contentData['title'] = FormSecurity::decodeByText($contentData['title']);    

    $filename = $contentData['filename'];
    $filetype = $contentData['filetype'];
    $filesize = $contentData['filesize'];

    switch ($contentsType) {
      case 'text':
        $contentData['contents'] = FormSecurity::decodeByText($contentData['contents']);
        break;
      case 'html':
        $contentData['contents'] = FormSecurity::decodeByHtml($contentData['contents']);    
        break;
    }

    $contentData['css_down'] = 'hide';
    $contentData['css_img'] = 'hide';

    if (isset($filename) && $filetype) {

      $fileupPath = $rootPath . "files/board/${filename}";
      if (($is_download === 'y') && preg_match( '/(application\/x-zip-compressed|application\/zip)+', $filetype)) {

        $contentData['css_down'] = 'sx-show';
      } else if (preg_match( '/(jpg|jpeg|gif|png)+/i', $filetype)){

        $imageInfo = getimagesize($fileupPath);
        $imageType = $imageInfo[2];

        if ($imageType === IMAGETYPE_JPEG) {
          $image = imagecreatefromjpeg($fileupPath);
        } elseif($imageType === IMAGETYPE_GIF) {
          $image = imagecreatefromgif($fileupPath);
        } elseif($imageType === IMAGETYPE_PNG) {
          $image = imagecreatefrompng($fileupPath);
        }

        $contentData['css_img'] = 'sx-show';
        $contentData['css_img_width'] = imagesx($image) . 'px';
      }
      $contentData['fileup_name'] = $filename;
      $contentData['fileup_path'] = $fileupPath;
    }

    // opkey
    $contentData['css_progress_step'] = 'hide';
    if (($is_progress_step === 'y') || ($grade > 9)) {
      $contentData['css_progress_step'] = 'show';
      $progressSteps = array(
        '진행완료'=>'progress_step_done',
        '진행중'=>'progress_step_ing',
        '입금완료'=>'progress_step_charged',
        '미입금'=>'progress_step_nocharged',
        '메일발송'=>'progress_step_sended',
        '초기화'=>'progress_step_reset'
      );

      $stepKey = strtolower($contentData['progress_step']);     
      $contentData[$progressSteps[$stepKey]] = 'checked';
    }

    // comment
    $contentData['css_comment'] = 'hide';
    $commentData = array();   
    if ($is_comment === 'y') {
      $contentData['css_comment'] = 'show';

      $where->reset();
      $where->set('contents_id',$id,'=');
      $this->model->select('comment','*', $where);
      $commentData['num'] = $this->model->getNumRows();
      $commentData['list'] = $this->model->getRows();
    }

    $this->document_data['group'] = $groupData;
    $this->document_data['contents'] = $contentData;
    $this->document_data['comments'] = $commentData;
    $this->document_data['category'] = $category;

    $this->skin_path_list['root'] =$rootPath;
    $this->skin_path_list['path'] = $skinPath;
    $this->skin_path_list['realPath'] = $skinRealPath;
    $this->skin_path_list['header'] = $headerPath;    
    $this->skin_path_list['contents'] = "{$skinRealPath}read.tpl";
    $this->skin_path_list['footer'] = $footerPath;
    $this->skin_path_list['comment'] =  "{$skinRealPath}_comment.tpl";
    $this->skin_path_list['progress_step'] =  "{$skinRealPath}_progress_step.tpl"; 

    $this->output();    
  }

  function displayWrite() {

    $UIError = UIError::getInstance();

    $context = Context::getInstance();
    $this->request_data = $context->getRequestAll();
    $this->session_data = $context->getSessionAll();  
    $category = $context->getParameter('category');

    $find = $this->request_data['find'];
    $search = $this->request_data['search'];

    $returnURL = $context->getServer('HTTP_REFERER');
    $returnURL = urldecode($returnURL);
    if (isset($search) && $search) {
      $returnURL .= "?find=${find}&search=${search}";
    }
    
    $this->document_data['jscode'] = 'write';
    $this->document_data['module_code'] = 'board';
    $this->document_data['module_name'] = '게시판 쓰기';

    $grade = $this->session_data['grade'];
    $user_id = $this->session_data['user_id'];
    $user_name = $this->session_data['user_name'];
    if (empty($user_name)) {
      $user_name = $this->session_data['nick_name'];
    }    
    $password = $this->session_data['password'];
    $PHP_SELF = $context->getServer("PHP_SELF");
    $admin_pass = $context->checkAdminPass();

    $where = new QueryWhere();
    $where->set('category',$category,'=');
    $this->model->select('board_group', '*', $where);

    $groupData = $this->model->getRow();
    $nonemember = $groupData['allow_nonmember'];
    $grade_w = $groupData['grade_w'];
    $is_writable   = $groupData['is_writable'];
    $headerPath = $groupData['header_path'];
    $skinName = $groupData['skin_path'];
    $footerPath = $groupData['footer_path'];

    /**
     * css, js file path handler
     */
    $rootPath = _SUX_ROOT_;
    $skinPath = _SUX_ROOT_ . "modules/board/skin/${skinName}/";
    $skinRealPath = _SUX_PATH_ . "modules/board/skin/${skinName}/";
    $this->document_data['category'] = $category;

    $headerPath =Utils::convertAbsolutePath($headerPath, _SUX_PATH_);
    $footerPath = Utils::convertAbsolutePath($footerPath, _SUX_PATH_);

    if (!is_readable($headerPath)) {
      $headerPath = "{$skinRealPath}/_header.tpl";
      $UIError->add('상단 파일경로가 올바르지 않습니다.');
    }

    if (!is_readable($footerPath)) {
      $footerPath = "{$skinRealPath}/_footer.tpl";
      $UIError->add('하단 파일경로가 올바르지 않습니다.');
    }

    $where = new QueryWhere();
    if (isset($search) && $search) {
      $where->set($find, $search, 'like');
    }   
    $where->set('category', $category, '=');
    $this->model->select('board', 'wall', $where, 'id desc' , 0, 1);

    $contentData = $this->model->getRow();
    $wall = $contentData['wall'];   

    if ($wall === 'a' || !isset($wall)) {
      $contentData['wallname'] = "나라사랑";
      $contentData['wallkey'] = "b";
    } else if ($wall === 'b') {
      $contentData['wallname'] = "조국사랑";
      $contentData['wallkey'] = "a";
    }

    $contentsType = $contentData['contents_type'];
    $contentData['contents_type_' . $contentsType] = 'checked';
    
    if (isset($grade) && $grade) {
      $level = $grade;
    } else {
      $level = 0;
    }

    if ($level < $grade_w) {
      $msg .= '죄송합니다. 쓰기 권한이 없습니다.';    
      UIError::alertTo( $msg, true, array('url'=>$returnURL, 'delay'=>3));
      exit;
    }

    if ($nonemember === 'n') {
      if (empty($user_name)) {
        $returnToURL = $rootPath . $category . '/write';
        $msg = '죄송합니다. 이곳은 회원 전용 게시판 입니다.<br>로그인을 먼저 하세요.';
        UIError::alertTo( $msg, true, array('url'=>$rootPath . 'login?return_url=' . $returnToURL, 'delay'=>3));
      } 
    }

    if ($is_writable === 'n') {
      if ($admin_pass === FALSE) {
        $msg = '죄송합니다. 이곳은 관리자 전용게시판입니다.';
        UIError::alertTo( $msg, true, array('url'=>$returnURL, 'delay'=>3));
        exit;
      }
    }

    if (isset($user_name) && $user_name) {
      $contentData['css_user_label'] = 'sx-hide';
      $contentData['user_name_type'] = 'hidden';
      $contentData['user_pass_type'] = 'hidden';
      $contentData['user_id'] = empty($user_id) ? 'Guest': $user_id;
      $contentData['user_name'] = empty($user_name) ? 'Guest': $user_name;
      $contentData['user_password'] = $password;
    } else {
      $contentData['css_user_label'] = 'sx-show-inline';      
      $contentData['user_name_type'] = 'text';
      $contentData['user_pass_type'] = 'password';
      $contentData['user_id'] = empty($user_id) ? 'Guest': $user_id;
      $contentData['user_name'] = empty($user_name) ? 'Guest': $user_name;
      $contentData['user_password'] = '';
    }

    $this->document_data['group'] = $groupData;
    $this->document_data['contents'] = $contentData;

    $this->skin_path_list['root'] = $rootPath;
    $this->skin_path_list['path'] = $skinPath;
    $this->skin_path_list['realPath'] = $skinRealPath;    
    $this->skin_path_list['header'] = $headerPath;    
    $this->skin_path_list['contents'] = "{$skinRealPath}/write.tpl";
    $this->skin_path_list['footer'] = $footerPath;    

    $this->output();
  }

  function displayModify() {

    $UIError = UIError::getInstance();
    $context = Context::getInstance();
    
    $this->session_data = $context->getSessionAll();
    $this->request_data = $context->getRequestAll();

    $category = $context->getParameter('category');
    $id = $context->getParameter('id');

    $this->document_data['category'] = $category;
    $this->document_data['id'] = $id;

    $find = $this->request_data['find'];
    $search = $this->request_data['search'];

    $returnURL = $context->getServer('HTTP_REFERER');
    $returnURL = urldecode($returnURL);
    if (isset($search) && $search) {
      $returnURL .= "?find=${find}&search=${search}";
    }

    $this->document_data['jscode'] = 'modify';
    $this->document_data['module_code'] = 'board';
    $this->document_data['module_name'] = '게시판 수정';

    $grade = $this->session_data['grade']; 
    $user_id = $this->session_data['user_id'];    
    $user_name = $this->session_data['user_name'];
    if (empty($user_name)) {
      $user_name = $this->session_data['nick_name'];
    }   
    $password = $this->session_data['password'];  
    $PHP_SELF = $context->getServer("PHP_SELF");
    $admin_pass = $context->checkAdminPass(); 

    $where = new QueryWhere();
    $where->set('category',$category,'=');
    $this->model->select('board_group', '*', $where);

    $groupData = $this->model->getRow();
    $grade_m = $groupData['grade_m'];
    $nonemember = $groupData['allow_nonmember'];
    $is_modifiable = $groupData['is_modifiable'];
    $is_progress_step = $groupData['is_progress_step'];
    $headerPath =  $groupData['header_path'];
    $skinName =  $groupData['skin_path'];
    $footerPath =  $groupData['footer_path'];

    /**
     * css, js file path handler
     */
    $rootPath = _SUX_ROOT_;
    $skinPath = _SUX_ROOT_ . "modules/board/skin/${skinName}/";
    $skinRealPath = _SUX_PATH_ . "modules/board/skin/${skinName}/";
    $this->document_data['uri'] = $rootPath.$category;

    $headerPath =Utils::convertAbsolutePath($headerPath, _SUX_PATH_);
    $footerPath = Utils::convertAbsolutePath($footerPath, _SUX_PATH_);

    if (!is_readable($headerPath)) {
      $headerPath = "{$skinRealPath}/_header.tpl";
      $UIError->add('상단 파일경로가 올바르지 않습니다.');
    }

    if (!is_readable($footerPath)) {
      $footerPath = "{$skinRealPath}/_footer.tpl";
      $UIError->add('하단 파일경로가 올바르지 않습니다.');
    }

    $where->reset();
    $where->set('id', $id, '=');
    $this->model->select('board', '*', $where);

    $contentData = $this->model->getRow();
    $contentData['user_name'] = htmlspecialchars($contentData['user_name']);
    $contentData['title'] = nl2br($contentData['title']);
    $contentData['contents'] = FormSecurity::decode($contentData['contents']);
    
    $contentsType = $contentData['contents_type'];
    $contentData['contents_type_' . $contentsType] = 'checked';
    unset($contentData['password']);

    $where = new QueryWhere();
    if (isset($search) && $search) {
      $where->set($find, $search, 'like');
    }
    $where->set('category', $category, '=');
    $this->model->select('board', 'wall', $where, 'id desc' , 0, 1);

    $row = $this->model->getRow();
    $wall = $row['wall']; 
    if ($wall === 'a' || empty($wall)) {
      $contentData['wallname'] = "나라사랑";
      $contentData['wallkey'] = "b";
    } else if ($wall === 'b') {
      $contentData['wallname'] = "조국사랑";
      $contentData['wallkey'] = "a";
    }

    if (isset($grade) && $grade) {
      $level = $grade;
    } else {
      $level = 0;
    }

    if ($level < $grade_m) {
      $msg = '죄송합니다. 수정권한이 없습니다.';
      UIError::alertTo( $msg, true, array('url'=>$returnURL, 'delay'=>3));
      exit;
    }

    if ($nonemember === 'n') {
      if (empty($user_name)) {
        $returnToURL = $rootPath . $category . ' / '. $id . '/modify';
        $msg = '죄송합니다. 이곳은 회원 전용 게시판 입니다.<br>로그인을 먼저 하세요.';
        UIError::alertTo( $msg, true, array('url'=>$rootPath . 'login?return_url=' . $returnToURL, 'delay'=>3));
      } 
    }

    if ($is_modifiable === 'n') {
      if ($admin_pass === false) {
        $msg = '죄송합니다. 이곳은 관리자 전용 게시판입니다.';
        UIError::alertTo( $msg, true, array('url'=>$returnURL, 'delay'=>3));
      }
    }

    $this->document_data['group'] = $groupData;
    $this->document_data['contents'] = $contentData;

    $this->skin_path_list['root'] =$rootPath;
    $this->skin_path_list['path'] = $skinPath;
    $this->skin_path_list['realPath'] = $skinRealPath; 
    $this->skin_path_list['header'] = $headerPath;    
    $this->skin_path_list['contents'] = "{$skinRealPath}/modify.tpl";
    $this->skin_path_list['footer'] = $footerPath;    

    $this->output();
  }

  function displayReply() {

    $UIError = UIError::getInstance();
    $context = Context::getInstance();
    $this->request_data = $context->getRequestAll();
    $this->session_data = $context->getSessionAll();

    $find = $this->request_data['find'];
    $search = $this->request_data['search'];
    $category = $context->getParameter('category');
    $id = $context->getParameter('id');
    $this->document_data['category'] = $category;
    $this->document_data['id'] = $id;

    $returnURL = $context->getServer('HTTP_REFERER');
    $returnURL = urldecode($returnURL);
    if (isset($search) && $search) {
      $returnURL .= "?find=${find}&search=${search}";
    }

    $this->document_data['jscode'] = 'reply';
    $this->document_data['module_code'] = 'board';
    $this->document_data['module_name'] = '게시판 답변';

    $grade = $this->session_data['grade'];
    $user_id = $this->session_data['user_id'];
    $user_name = $this->session_data['user_name'];
    if (empty($user_name)) {
      $user_name = $this->session_data['nick_name'];
    }   
    $password = $this->session_data['password'];
    $PHP_SELF = $context->getServer("PHP_SELF");
    $admin_pass = $context->checkAdminPass();

    $where = new QueryWhere();
    $where->set('category',$category,'=');
    $this->model->select('board_group', '*', $where);

    $groupData = $this->model->getRow();
    $is_progress_step = $groupData['is_progress_step'];
    $grade_re = $groupData["grade_re"];
    $is_repliable = $groupData["is_repliable"];
    $headerPath = $groupData['header_path'];
    $skinName = $groupData['skin_path'];
    $footerPath = $groupData['footer_path'];

    /**
     * css, js file path handler
     */
    $rootPath = _SUX_ROOT_;
    $skinPath = _SUX_ROOT_ . "modules/board/skin/${skinName}/";
    $skinRealPath = _SUX_PATH_ . "modules/board/skin/${skinName}/";   
    $this->document_data['uri'] = $rootPath.$category;

    $headerPath =Utils::convertAbsolutePath($headerPath, _SUX_PATH_);
    $footerPath = Utils::convertAbsolutePath($footerPath, _SUX_PATH_);

    if (!is_readable($headerPath)) {
      $headerPath = "{$skinRealPath}/_header.tpl";
      $UIError->add('상단 파일경로가 올바르지 않습니다.');
    }

    if (!is_readable($footerPath)) {
      $footerPath = "{$skinRealPath}/_footer.tpl";
      $UIError->add('하단 파일경로가 올바르지 않습니다.');
    }

    $where->reset();
    $where->set('id',$id,'=');
    $this->model->select('board', '*', $where);

    $contentData = $this->model->getRow();    
    $contentData['user_name'] = empty($user_name) ? 'Guest' : $user_name;
    $contentData['title'] = htmlspecialchars($contentData['title']);
    $contentsType = trim($contentData['conetents_type']);

    $is_download = $contentData['is_download'];
    $filename = $contentData['filename'];
    $filetype = $contentData['filetype'];
    
    if ($contentsType === 'html'){
      $contentData['contents'] = htmlspecialchars_decode($contentData['contents']);
    }else if ($contentsType === 'text'){
      $contentData['contents'] = nl2br(htmlspecialchars($contentData['contents']));
    }
    
    $contentData['css_down'] = 'hide';
    $contentData['css_img'] = 'hide';

    $fileupPath = '';
    if ($filename) {

      $fileupPath = $rootPath . "files/board/${filename}";
      if (($is_download == 'y') && ($filetype === ("application/x-zip-compressed" || "application/zip"))) {

        $contentData['css_down'] = 'sx-show';
      } else if ($filetype !== ("application/x-zip-compressed" || "application/zip")){

        $image_info = getimagesize($fileupPath);
            $image_type = $image_info[2];

            if ( $image_type === IMAGETYPE_JPEG ) {
              $image = imagecreatefromjpeg($fileupPath);
            } elseif( $image_type === IMAGETYPE_GIF ) {
              $image = imagecreatefromgif($fileupPath);
            } elseif( $image_type === IMAGETYPE_PNG ) {
              $image = imagecreatefrompng($fileupPath);
        }
        $contentData['css_img'] = 'sx-show';
        $contentData['img_width'] = imagesx($image) . 'px';
      }
      $contentData['fileup_name'] = $filename;
      $contentData['fileup_path'] = $fileupPath;
    }

    $where->reset();
    $where->set('category', $category, '=');
    $this->model->select('board', 'wall', $where, 'id desc', 0, 1);

    $row = $this->model->getRow();      
    $wall = $row['wall'];
    if ($wall === 'a' || !isset($wall)) {
      $contentData['wallname'] = "나라사랑";
      $contentData['wallkey'] = "b";
    } else if ($wall === 'b') {
      $contentData['wallname'] = "조국사랑";
      $contentData['wallkey'] = "a";
    }

    $contentsType = $contentData['contents_type'];
    $contentData['contents_type_' . $contentsType] = 'checked';
    
    if (isset($grade) && $grade) {
      $level = $grade;
    } else {
      $level = 0;
    }

    if ($level < $grade_re) {
      $msg = '죄송합니다. 답변권한이 없습니다.';
      UIError::alertTo( $msg, true, array('url'=>$returnURL, 'delay'=>3));
      exit;
    }

    // 비회원 허용 유무 
    if ($is_progress_step !== 'y') {
      if (!isset($user_name) && $user_name == '') {
        $returnToURL = $rootPath . $category . '/'. $id . '/reply' ;
        $msg = '죄송합니다. 이곳은 회원 전용 게시판 입니다.<br>로그인을 먼저 하세요.';
        UIError::alertTo( $msg, true, array('url'=>$rootPath . 'login?return_url=' . $returnToURL, 'delay'=>3));
      } 
    }

    if ($is_repliable === 'n') {
      if ($admin_pass == false) {
        $msg = '죄송합니다. 이곳은 관리지 전용게시판입니다.';
        UIError::alertTo( $msg, true, array('url'=>$returnURL, 'delay'=>3));
        exit;
      }
    }

    if (isset($user_name) && $user_name) {
      $contentData['css_user_label'] = 'sx-hide';
      $contentData['user_name_type'] = 'hidden';
      $contentData['user_pass_type'] = 'hidden';
      $contentData['user_id'] = empty($user_id) ? 'guest' : $user_id;
      $contentData['user_name'] = empty($user_name) ? 'Guest' : $user_name;
      $contentData['user_password'] = $password;
    } else {
      $contentData['css_user_label'] = 'sx-show-inline';      
      $contentData['user_name_type'] = 'text';
      $contentData['user_id'] = empty($user_id) ? 'guest' : $user_id;
      $contentData['user_name'] = empty($user_name) ? 'Guest' : $user_name;
      $contentData['user_pass_type'] = 'password';
      $contentData['user_password'] = '';
    }

    $this->document_data['group'] = $groupData;
    $this->document_data['contents'] = $contentData;

    $this->skin_path_list['root'] =$rootPath;
    $this->skin_path_list['path'] = $skinPath;
    $this->skin_path_list['realPath'] = $skinRealPath; 
    $this->skin_path_list['header'] = $headerPath;    
    $this->skin_path_list['contents'] = "{$skinRealPath}/reply.tpl";
    $this->skin_path_list['footer'] = $footerPath;    

    $this->output();
  }

  function displayDelete() {

    $UIError = UIError::getInstance();
    $context = Context::getInstance();

    $category = $context->getParameter('category');
    $id = $context->getParameter('id');

    $this->document_data['category'] = $category;
    $this->document_data['id'] = $id;

    $this->document_data['jscode'] = 'delete';
    $this->document_data['module_code'] = 'board';
    $this->document_data['module_name'] = '게시물 삭제'; 

    $where = new QueryWhere();
    $where->set('category', $category, '=');
    $this->model->select('board_group', '*');

    $groupData = $this->model->getRow();
    $headerPath = $groupData['header_path'];
    $skinName = $groupData['skin_path'];
    $footerPath = $groupData['footer_path'];

    /**
     * css, js file path handler
     */
    $rootPath = _SUX_ROOT_;
    $skinPath = _SUX_ROOT_ . "modules/board/skin/${skinName}/";
    $skinRealPath = _SUX_PATH_ . "modules/board/skin/${skinName}/";   
    $this->document_data['uri'] = $rootPath.$category;

    $headerPath =Utils::convertAbsolutePath($headerPath, _SUX_PATH_);
    $footerPath = Utils::convertAbsolutePath($footerPath, _SUX_PATH_);

    if (!is_readable($headerPath)) {
      $headerPath = "{$skinRealPath}/_header.tpl";
      $UIError->add('상단 파일경로가 올바르지 않습니다.');
    }

    if (!is_readable($footerPath)) {
      $footerPath = "{$skinRealPath}/_footer.tpl";
      $UIError->add('하단 파일경로가 올바르지 않습니다.');
    }
    
    $where->reset();
    $where->set('id', $id, '=');
    $this->model->select('board', 'id, category, user_name', $where);
    $contentData = $this->model->getRow();

    $this->document_data['group'] = $groupData;
    $this->document_data['contents'] = $contentData;

    $this->skin_path_list['root'] =$rootPath;
    $this->skin_path_list['path'] = $skinPath;
    $this->skin_path_list['realPath'] = $skinRealPath; 
    $this->skin_path_list['header'] = $headerPath;    
    $this->skin_path_list['contents'] = "{$skinRealPath}/delete.tpl";
    $this->skin_path_list['footer'] = $footerPath;    

    $this->output();
  }

  function displayDeleteComment() {

    $UIError = UIError::getInstance();
    $context = Context::getInstance();

    $category = $context->getParameter('category');
    $mid = $context->getParameter('id');  // 메인 아이디
    $id = $context->getParameter('sid');    // 서브 아이디 

    $this->document_data['category'] = $category;
    $this->document_data['mid'] = $mid;

    $this->document_data['jscode'] ='delete';
    $this->document_data['module_code'] = 'board';
    $this->document_data['module_name'] = '게시물 삭제';

    $where = new QueryWhere();
    $where->set('category', $category, '=');
    $this->model->select('board_group', '*', $where);

    $groupData = $this->model->getRow();
    $headerPath = $groupData['header_path'];
    $skinName = $groupData['skin_path'];
    $footerPath = $groupData['footer_path'];

    /**
     * css, js file path handler
     */
    $rootPath = _SUX_ROOT_;
    $skinPath = _SUX_ROOT_ . "modules/board/skin/${skinName}/";
    $skinRealPath = _SUX_PATH_ . "modules/board/skin/${skinName}/";   

    $headerPath =Utils::convertAbsolutePath($headerPath, _SUX_PATH_);
    $footerPath = Utils::convertAbsolutePath($footerPath, _SUX_PATH_);

    if (!is_readable($headerPath)) {
      $headerPath = "{$skinRealPath}/_header.tpl";
      $UIError->add('상단 파일경로가 올바르지 않습니다.');
    }

    if (!is_readable($footerPath)) {
      $footerPath = "{$skinRealPath}/_footer.tpl";
      $UIError->add('하단 파일경로가 올바르지 않습니다.');
    }

    $where->reset();
    $where->set('id', $id, '=');
    $this->model->select('comment', '*', $where);

    $contentData = $this->model->getRow();
    $contentData['id'] = $id;
    $this->document_data['group'] = $groupData;
    $this->document_data['contents'] = $contentData;

    $this->skin_path_list['root'] =$rootPath;
    $this->skin_path_list['path'] = $skinPath;
    $this->skin_path_list['realPath'] = $skinRealPath; 
    $this->skin_path_list['header'] = $headerPath;    
    $this->skin_path_list['contents'] = "{$skinRealPath}/delete_comment.tpl";
    $this->skin_path_list['footer'] = $footerPath;  

    $this->output();
  }
}