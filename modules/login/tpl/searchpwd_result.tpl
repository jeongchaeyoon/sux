<div class="articles"> 
  <div class="sx_login">
    <h1>비밀번호 찾기 결과</h1>
    <p class="sx_subtitle">SUX Board 솔루션을 이용해 주셔서 진심으로 감사합니다.</p>
    
    <div class="sx_login_box sx-edgebox-2px">
      <div class="sx_login_header sx-form-group">
        <i class="xi-info-o xi-2x"></i>
        <label for="emptyId" class="sx-control-label">조회 결과</label>
      </div>
      <div class="sx_login_body sx-form-group">
        <p class="text-center"><span>' {$documentData.user_email} '</span></p>
        <p class="text-center"><strong>이메일</strong> 주소로<br><strong>임시 비밀번호</strong>가 발송되었습니다.</p>
      </div>

      <div class="sx_btn_group sx-form-group">
        <input type="button" name="btn_confirm" value="확 인" onclick="location.href='{$rootPath}login'" class="sx-btn sx-btn-block">
      </div>
      <div class="sx_login_footer">
        <a href="{$rootPath}member-join">회원가입</a><span>|</span><a href="{$rootPath}search-id">아이디 찾기</a>
      </div>
    </div>
    <div class="notice_panel">
      기타 궁금한 사항이나 질문은 Q&amp;A 게시판을 이용해 주세요.
    </div>
  </div>      
</div>
