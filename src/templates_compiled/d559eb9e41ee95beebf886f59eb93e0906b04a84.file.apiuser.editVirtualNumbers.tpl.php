<?php /* Smarty version Smarty-3.0.5, created on 2016-09-15 03:12:50
         compiled from "/var/www/html/sms-api-admin/src/templates/apiuser.editVirtualNumbers.tpl" */ ?>
<?php /*%%SmartyHeaderCode:74085966957da11b2c4cc40-62759502%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd559eb9e41ee95beebf886f59eb93e0906b04a84' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/apiuser.editVirtualNumbers.tpl',
      1 => 1473907559,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '74085966957da11b2c4cc40-62759502',
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
				<th class="zebra-odd">Destination</th>
				<th class="zebra-even">Forward URL</th>
				<th class="zebra-odd">Forward Status</th>
				<th class="zebra-even">
					<a href="#" onclick="$app.module('apiuser').addVirtualNumber(<?php echo $_smarty_tpl->getVariable('userID')->value;?>
)" class="form-button">
						<img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
						<span class="form-button-text">Add New</span>
					</a>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['name'] = 'vnum';
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('virtualNumber')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['vnum']['total']);
?>
			<tr class="<?php echo smarty_function_cycle(array('values'=>"zebra-odd,zebra-even"),$_smarty_tpl);?>
">
				<td class="type-phone"><?php echo $_smarty_tpl->getVariable('virtualNumber')->value[$_smarty_tpl->getVariable('smarty')->value['section']['vnum']['index']]['virtualDestination'];?>
</td>
				<td class="type-url"><?php echo $_smarty_tpl->getVariable('virtualNumber')->value[$_smarty_tpl->getVariable('smarty')->value['section']['vnum']['index']]['virtualUrl'];?>
</td>
				<td class="type-status"><?php echo $_smarty_tpl->getVariable('virtualNumber')->value[$_smarty_tpl->getVariable('smarty')->value['section']['vnum']['index']]['virtualUrlStatusName'];?>
</td>
				<td class="type-action">
					<a href="#" onclick="$app.module('apiuser').editVirtualNumber(<?php echo $_smarty_tpl->getVariable('virtualNumber')->value[$_smarty_tpl->getVariable('smarty')->value['section']['vnum']['index']]['virtualNumberID'];?>
, <?php echo $_smarty_tpl->getVariable('userID')->value;?>
)" class="form-button"><img src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" /></a>
					<a href="#" onclick="$app.module('apiuser').removeVirtualNumber(<?php echo $_smarty_tpl->getVariable('virtualNumber')->value[$_smarty_tpl->getVariable('smarty')->value['section']['vnum']['index']]['virtualNumberID'];?>
, <?php echo $_smarty_tpl->getVariable('userID')->value;?>
)" class="form-button"><img src="skin/images/icon-remove.png" class="icon-image icon-size-small" alt="" /></a>
				</td>
			</tr>
			<?php endfor; endif; ?>
		</tbody>
	</table>
</fieldset>