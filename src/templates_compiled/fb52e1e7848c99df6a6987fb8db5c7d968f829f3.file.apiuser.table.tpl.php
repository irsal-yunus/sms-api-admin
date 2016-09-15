<?php /* Smarty version Smarty-3.0.5, created on 2016-09-15 02:54:49
         compiled from "/var/www/html/sms-api-admin/src/templates/apiuser.table.tpl" */ ?>
<?php /*%%SmartyHeaderCode:96136358457da0d793bc891-03533662%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fb52e1e7848c99df6a6987fb8db5c7d968f829f3' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/apiuser.table.tpl',
      1 => 1473907558,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '96136358457da0d793bc891-03533662',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_cycle')) include '/var/www/html/sms-api-admin/src/lib/com/smarty/plugins/function.cycle.php';
?><form action="#" class="admin-stealthform">
<table id="apiuser-simpletable" class="admin-table">
	<?php if ($_smarty_tpl->getVariable('options')->value['onlySpecifiedClient']){?>
	<caption>API Users of Client "<strong><?php echo $_smarty_tpl->getVariable('client')->value['companyName'];?>
</strong>"</caption>
	<?php }?>
	<thead>
		<tr>
			<th class="type-nav" colspan="5">
				<?php if (isset($_smarty_tpl->getVariable('options',null,true,false)->value['onlyActiveUser'])&&$_smarty_tpl->getVariable('options')->value['onlyActiveUser']){?>
				<a href="#" class="form-button" onclick="var arg = $.extend({}, <?php echo htmlentities($_smarty_tpl->getVariable('optionsJson')->value);?>
, { onlyActiveUser:false}); $app.module('apiuser').showUserList(arg);">
					<span class="form-button-text">Include Inactive User</span>
				</a>
				<?php }else{ ?>
				<a href="#" class="form-button" onclick="var arg = $.extend({}, <?php echo htmlentities($_smarty_tpl->getVariable('optionsJson')->value);?>
, { onlyActiveUser:true}); $app.module('apiuser').showUserList(arg);">
					<span class="form-button-text">Show Only Active User</span>
				</a>
				<?php }?>
			</th>
		</tr>
		<tr>
			<th style="width: 20%;">Account Name</th>
			<th style="width: 20%;">Client Name</th>
			<th style="width: 10%;">Balance</th>
			<th style="width: 10%;">Status</th>
			<th style="width: 40%;">
					<?php if ($_smarty_tpl->getVariable('options')->value['onlySpecifiedClient']){?>
					<a href="#" class="form-button" onclick="$app.module('apiuser').createUser(<?php echo $_smarty_tpl->getVariable('options')->value['clientID'];?>
);">
						<img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
					<span class="form-button-text">Register</span>
					<?php }else{ ?>
					<a href="#" class="form-button" onclick="$app.module('apiuser').createUser();">
						<img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
					<span class="form-button-text">Register</span>
					<?php }?>
				</a>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="9">
				&nbsp;
			</th>
		</tr>
	</tfoot>
	<tbody>
		<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['list']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['name'] = 'list';
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('users')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['total']);
?>
		<tr class="<?php echo smarty_function_cycle(array('values'=>"zebra-odd,zebra-even"),$_smarty_tpl);?>
<?php if ($_smarty_tpl->getVariable('users')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['userID']==$_smarty_tpl->getVariable('options')->value['highlight']){?> table-row-highlight<?php }?>">
			<td class="type-text"><?php echo $_smarty_tpl->getVariable('users')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['userName'];?>
</td>
			<td class="type-text"><?php echo $_smarty_tpl->getVariable('users')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['clientCompanyName'];?>
</td>
			<td class="type-counter"><?php echo $_smarty_tpl->getVariable('users')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['userCredit'];?>
</td>
			<td class="type-status"><?php echo $_smarty_tpl->getVariable('users')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['statusName'];?>
</td>
			<td class="type-action">
				<?php ob_start();?><?php echo $_smarty_tpl->getVariable('users')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['active'];?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1){?>
				<a href="#" title="Deactivate" onclick="$app.module('apiuser').deactivateUser(<?php echo $_smarty_tpl->getVariable('users')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['userID'];?>
, true);" class="form-button"><img src="skin/images/icon-disable.png" class="icon-image" alt="" /></a>
				<?php }else{ ?>
				<a href="#" title="Activate" onclick="$app.module('apiuser').activateUser(<?php echo $_smarty_tpl->getVariable('users')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['userID'];?>
, true);" class="form-button"><img src="skin/images/icon-enable.png" class="icon-image" alt="" /></a>
				<?php }?>
				<a href="#" title="View Details" class="form-button" onclick="$app.module('apiuser').showUserDetails(<?php echo $_smarty_tpl->getVariable('users')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['userID'];?>
);"><img src="skin/images/icon-view.png" class="icon-image" alt="" /></a>
				<a href="#" title="Edit" class="form-button" onclick="$app.module('apiuser').editUser(<?php echo $_smarty_tpl->getVariable('users')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['userID'];?>
);"><img src="skin/images/icon-edit.png" class="icon-image" alt="" /></a>
				<a href="#" title="Manage Credit" class="form-button" onclick="$app.module('credit').manageUserCredit(<?php echo $_smarty_tpl->getVariable('users')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['userID'];?>
);"><img src="skin/images/icon-credit.png" class="icon-image" alt="" /></a>
                                <a href="#" title="Report" class="form-button" onclick="$app.module('apiuser').reportBilling(<?php echo $_smarty_tpl->getVariable('users')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['userID'];?>
);"><img src="skin/images/icon-history.png" class="icon-image" alt="" /></a>
			</td>
		</tr>
		<?php endfor; endif; ?>
	</tbody>
</table>
</form>