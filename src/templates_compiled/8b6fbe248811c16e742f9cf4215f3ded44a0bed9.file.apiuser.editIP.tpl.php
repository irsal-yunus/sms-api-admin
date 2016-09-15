<?php /* Smarty version Smarty-3.0.5, created on 2016-09-14 04:38:20
         compiled from "/var/www/html/sms-api-admin/src/templates/apiuser.editIP.tpl" */ ?>
<?php /*%%SmartyHeaderCode:36158857557d8d43c264ac2-64019801%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8b6fbe248811c16e742f9cf4215f3ded44a0bed9' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/apiuser.editIP.tpl',
      1 => 1473742353,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '36158857557d8d43c264ac2-64019801',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_cycle')) include '/var/www/html/sms-api-admin/src/lib/com/smarty/plugins/function.cycle.php';
?><fieldset>
	<table class="admin-simpletable">
		<thead>
			<tr>
				<th class="zebra-odd" style="width:65%;">Permitted IP</th>
				<th class="zebra-even" style="width:35%;">
					<a href="#" onclick="$app.module('apiuser').allowIP(<?php echo $_smarty_tpl->getVariable('userID')->value;?>
)" class="form-button">
						<img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
						<span class="form-button-text">Allow IP</span>
					</a>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['ip']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['name'] = 'ip';
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('permittedIP')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['total']);
?>
			<tr class="<?php echo smarty_function_cycle(array('values'=>"zebra-odd,zebra-even"),$_smarty_tpl);?>
">
				<td class="type-ip"><?php echo $_smarty_tpl->getVariable('permittedIP')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ip']['index']]['ipAddress'];?>
</td>
				<td class="type-action"><a href="#" onclick="$app.module('apiuser').disallowIP(<?php echo $_smarty_tpl->getVariable('userID')->value;?>
, '<?php echo $_smarty_tpl->getVariable('permittedIP')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ip']['index']]['ipAddress'];?>
')" class="form-button"><img src="skin/images/icon-remove.png" class="icon-image icon-size-small" alt="" /></a></td>
			</tr>
			<?php endfor; endif; ?>
		</tbody>
	</table>
</fieldset>