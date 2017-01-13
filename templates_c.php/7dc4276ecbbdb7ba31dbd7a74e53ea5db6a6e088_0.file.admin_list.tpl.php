<?php
/* Smarty version 3.1.30, created on 2017-01-05 10:57:45
  from "/Applications/MAMP/htdocs/sux/modules/board/tpl/admin_list.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_586e1899827039_40322565',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7dc4276ecbbdb7ba31dbd7a74e53ea5db6a6e088' => 
    array (
      0 => '/Applications/MAMP/htdocs/sux/modules/board/tpl/admin_list.tpl',
      1 => 1483518983,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_586e1899827039_40322565 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('rootPath', $_smarty_tpl->tpl_vars['skinPathList']->value['root']);
$_smarty_tpl->_assignInScope('headerPath', $_smarty_tpl->tpl_vars['skinPathList']->value['header']);
$_smarty_tpl->_assignInScope('footerPath', $_smarty_tpl->tpl_vars['skinPathList']->value['footer']);
$_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['headerPath']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>"SUX관리자 게시판 목록 - StreamUX"), 0, true);
?>

<div class="articles ui-edgebox">
	<div class="list">
		<div class="tt">
			<div class="imgbox">
				<h1>게시판목록</h1>
			</div>
		</div>
		<div class="box">
			<ul>
				<li>
					<img src="<?php echo $_smarty_tpl->tpl_vars['rootPath']->value;?>
modules/admin/tpl/images/icon_notice.gif" width="30" height="13" align="absmiddle" class="icon-notice">
					<span class="text-notice">한번 삭제 시 모든 자료가 사라집니다. 주의하세요.</span>			
				</li>
			</ul>
			<table summary="회원목록을 보여줍니다." cellspacing="0">
				<caption><span class="blind">게시판목록</span></caption>
				<colgroup>
					<col width="8%">
					<col width="28%">
					<col width="26%">
					<col width="10%">
					<col width="12%">
					<col width="8%">
					<col width="8%">
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><span>번호</span></th>
						<th scope="col"><span>게시판 이름</span></th>
						<th scope="col"><span>생성날</span></th>
						<th scope="col"><span>권한</span></th>
						<th scope="col"><span>넓이</span></th>
						<th scope="col"><span>수정</span></th>
						<th scope="col"><span>삭제</span></th>
					</tr>         
				</thead>
				<tbody id="boardList">
					<tr>
						<td colspan="7"></td>
					</tr>
					<!--
					@ jquery templete
					@ name	boardWarnMsg_tmpl, boardList_tmpl
					-->								
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php echo '<script'; ?>
 type="jquery-templete" id="boardWarnMsg_tmpl">

	<tr>
		<td colspan="9"><span class="warn-msg">${msg}</span></td>
	</tr>

<?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="jquery-templete" id="boardList_tmpl">
	<tr>
		<td><span>${no}</span></td>
		<td>
			<a href="<?php echo $_smarty_tpl->tpl_vars['rootPath']->value;?>
${category}" target="_blank"><span>${board_name}</span></a>
		</td>
										
		<td><span>${$item.editDate(date)}</span></td>		
		<td><span>${allow_nonmember}</span></td>						
		<td><span>${board_width}</span></td>
		
		<td>
			<a href="<?php echo $_smarty_tpl->tpl_vars['rootPath']->value;?>
board-admin/${id}/modify">
				<img src="<?php echo $_smarty_tpl->tpl_vars['rootPath']->value;?>
modules/admin/tpl/images/btn_edit.gif" alt="수정버튼">
			</a></td>
		<td>
			<a href="<?php echo $_smarty_tpl->tpl_vars['rootPath']->value;?>
board-admin/${id}/delete">
				<img src="<?php echo $_smarty_tpl->tpl_vars['rootPath']->value;?>
modules/admin/tpl/images/btn_del.gif" alt="삭제버튼">
			</a>
		</td>
	</tr>
<?php echo '</script'; ?>
>
<?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['footerPath']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

<?php }
}
