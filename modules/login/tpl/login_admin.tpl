{assign var=skinDir value=$skinPathList.dir}
{include file="$skinDir/_header.tpl" title="SUX관리자 로그인 - StreamUX"}
<div class="wrapper">
	<div class="header">
		<div class="util"></div>
		<h1 class="logo">
			<img src="tpl/images/logo.png" alt="streamxux">
		</h1>
	</div>
	<div class="container">		
		<div class="article-box ui-edgebox">
			<div class="login">
				<h1 class="title">관리자 로그인</h1>
				<span class="subtitle">SUX 솔루션을 이용해 주셔서 진심으로 감사합니다.</span>

				<form action="login.admin.php?action=logpass" name="f_login_admin" method="post" onSubmit="return jsux.fn.loginAdmin.checkForm(this);">
				<div class="box ui-edgebox-2px">
					<div class="login-header">
						<img src="tpl/images/icon_01.gif" title="">						
						<span>관리자 확인</span>
					</div>
					<div class="login-body">
						<div class="panel-info">
							<ul>
								<li><span class="ui-label">아이디</span><input type="text" name="user_id" maxlength="14" value="admin"></li>
								<li><span class="ui-label">비밀번호</span><input type="password" name="user_pwd" maxlength="20"></li>
							</ul>							
						</div><div class="panel-btn">
							<input type="image" name="imagefield" src="tpl/images/btn_login.gif" title="로그인버튼" class="login-btn">
						</div>					
					</div>													
				</div>
				</form>
				<div class="panel-fail">
					<ul>
						<li><span>아이디와 비밀번호가 일치하지 않습니다.</span></li>
						<li><span>아이디와 비밀번호를 정확하게 입력해주세요.</span></li>
						<li>만일 회원가입을 하지 않고, 로그인을 하셨다면 회원가입을 먼저 해주세요.</li>
					</ul>
				</div>
				<div class="panel-notice">
					<dl>
						<dt>주의사항</dt>
						<dd>비밀번호가 노출되지 않도록 세심한 주의를 기울여 주세요.</dd>
					</dl><dl>
						<dt>서비스 이용안내</dt>
						<dd>서비스를 이용하시려면 먼저 로그인을 해주세요.</dd>
					</dl>
				</div>					
			</div>			
		</div>		
	</div>
	<div class="footer">
		{include file="$copyrightPath"}
	</div>
</div>
<div class="ui-panel-msg"></div>

<script type="x-jquery-templete" id="ljsMember_tmpl">
{literal}
	<option>${name}</option>
{/literal}
</script>
{include file="$skinDir/_footer.tpl"}
