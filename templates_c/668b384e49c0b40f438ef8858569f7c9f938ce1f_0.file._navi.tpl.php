<?php
/* Smarty version 3.1.31, created on 2017-08-30 10:35:06
  from "/Applications/MAMP/htdocs/sux/modules/board/skin/default/_navi.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_59a678ba4c73b8_27382439',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '668b384e49c0b40f438ef8858569f7c9f938ce1f' => 
    array (
      0 => '/Applications/MAMP/htdocs/sux/modules/board/skin/default/_navi.tpl',
      1 => 1488851712,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59a678ba4c73b8_27382439 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('pagination', $_smarty_tpl->tpl_vars['documentData']->value['pagination']);
if ($_smarty_tpl->tpl_vars['pagination']->value['okpage'] != 'yes') {?>
	<?php if ($_smarty_tpl->tpl_vars['requestData']->value['search'] != '') {?>
		<a href="<?php echo $_smarty_tpl->tpl_vars['uri']->value;?>
?passover=<?php echo $_smarty_tpl->tpl_vars['pagination']->value['prevpassover'];?>
&find=<?php echo $_smarty_tpl->tpl_vars['requestData']->value['find'];?>
&search=<?php echo $_smarty_tpl->tpl_vars['requestData']->value['search'];?>
">이전</a>
	<?php } else { ?>
		<a href="<?php echo $_smarty_tpl->tpl_vars['uri']->value;?>
?passover=<?php echo $_smarty_tpl->tpl_vars['pagination']->value['prevpassover'];?>
">이전</a>
	<?php }?>		
<?php }?>

<?php
$__section_page_0_saved = isset($_smarty_tpl->tpl_vars['__smarty_section_page']) ? $_smarty_tpl->tpl_vars['__smarty_section_page'] : false;
$__section_page_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['pagination']->value['nowpageend']) ? count($_loop) : max(0, (int) $_loop));
$__section_page_0_start = (int)@$_smarty_tpl->tpl_vars['pagination']->value['nowpage'] < 0 ? max(0, (int)@$_smarty_tpl->tpl_vars['pagination']->value['nowpage'] + $__section_page_0_loop) : min((int)@$_smarty_tpl->tpl_vars['pagination']->value['nowpage'], $__section_page_0_loop);
$__section_page_0_total = min(($__section_page_0_loop - $__section_page_0_start), $__section_page_0_loop);
$_smarty_tpl->tpl_vars['__smarty_section_page'] = new Smarty_Variable(array());
if ($__section_page_0_total != 0) {
for ($__section_page_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_page']->value['index'] = $__section_page_0_start; $__section_page_0_iteration <= $__section_page_0_total; $__section_page_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_page']->value['index']++){
?>
	<?php $_smarty_tpl->_assignInScope('index', (isset($_smarty_tpl->tpl_vars['__smarty_section_page']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_page']->value['index'] : null));
?>
	<?php $_smarty_tpl->_assignInScope('nowpassover', $_smarty_tpl->tpl_vars['pagination']->value['limit']*($_smarty_tpl->tpl_vars['index']->value-1));
?>

	<?php if ($_smarty_tpl->tpl_vars['pagination']->value['total'] > $_smarty_tpl->tpl_vars['nowpassover']->value) {?>
		<?php if ($_smarty_tpl->tpl_vars['pagination']->value['passover'] != $_smarty_tpl->tpl_vars['nowpassover']->value) {?>
			<?php if ($_smarty_tpl->tpl_vars['requestData']->value['search'] != '') {?>
				<a href="<?php echo $_smarty_tpl->tpl_vars['uri']->value;?>
?passover=<?php echo $_smarty_tpl->tpl_vars['nowpassover']->value;?>
&find=<?php echo $_smarty_tpl->tpl_vars['requestData']->value['find'];?>
&search=<?php echo $_smarty_tpl->tpl_vars['requestData']->value['search'];?>
">[<?php echo $_smarty_tpl->tpl_vars['index']->value;?>
]</a>
			<?php } else { ?>
				<a href="<?php echo $_smarty_tpl->tpl_vars['uri']->value;?>
?passover=<?php echo $_smarty_tpl->tpl_vars['nowpassover']->value;?>
">[<?php echo $_smarty_tpl->tpl_vars['index']->value;?>
]</a>
			<?php }?>	
		<?php } else { ?>
			&nbsp;<span class="color-red"><?php echo $_smarty_tpl->tpl_vars['index']->value;?>
</span>&nbsp
		<?php }?>
	<?php }
}
}
if ($__section_page_0_saved) {
$_smarty_tpl->tpl_vars['__smarty_section_page'] = $__section_page_0_saved;
}
?>

<?php if ($_smarty_tpl->tpl_vars['pagination']->value['total'] > $_smarty_tpl->tpl_vars['pagination']->value['hanpassoverpage']) {?>
	<?php if ($_smarty_tpl->tpl_vars['requestData']->value['search'] != '') {?>
		<a href="<?php echo $_smarty_tpl->tpl_vars['uri']->value;?>
?passover=<?php echo $_smarty_tpl->tpl_vars['pagination']->value['newpassover'];?>
&find=<?php echo $_smarty_tpl->tpl_vars['requestData']->value['find'];?>
&search=<?php echo $_smarty_tpl->tpl_vars['requestData']->value['search'];?>
">다음</a>
	<?php } else { ?>
		<a href="<?php echo $_smarty_tpl->tpl_vars['uri']->value;?>
?passover=<?php echo $_smarty_tpl->tpl_vars['pagination']->value['newpassover'];?>
">다음</a>
	<?php }
}
}
}
