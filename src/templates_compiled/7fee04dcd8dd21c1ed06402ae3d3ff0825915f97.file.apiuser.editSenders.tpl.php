<?php /* Smarty version Smarty-3.0.5, created on 2016-09-15 03:13:08
         compiled from "/var/www/html/sms-api-admin/src/templates/apiuser.editSenders.tpl" */ ?>
<?php /*%%SmartyHeaderCode:111096081157da11c4d66c81-92745215%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7fee04dcd8dd21c1ed06402ae3d3ff0825915f97' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/apiuser.editSenders.tpl',
      1 => 1473907559,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '111096081157da11c4d66c81-92745215',
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
				<th class="zebra-odd" style="width:20%;">Sender name</th>
				<th class="zebra-even" style="width:20%;">Range Start</th>
				<th class="zebra-odd" style="width:20%;">Range End</th>
                                <th class="zebra-even" style="width: 10%">Cobrander ID</th>
				<th class="zebra-odd" style="width:10%;">Status</th>           
				<th class="zebra-even" style="width:20%;">
					<a href="#" onclick="$app.module('apiuser').addSender(<?php echo $_smarty_tpl->getVariable('userID')->value;?>
)" class="form-button">
						<img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
						<span class="form-button-text">Add Sender</span>
					</a>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['sender']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['name'] = 'sender';
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('senderID')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['total']);
?>
			<tr class="<?php echo smarty_function_cycle(array('values'=>"zebra-odd,zebra-even"),$_smarty_tpl);?>
">
				<td class="type-text"><?php echo $_smarty_tpl->getVariable('senderID')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sender']['index']]['senderName'];?>
</td>
				<td class="type-phone"><?php echo $_smarty_tpl->getVariable('senderID')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sender']['index']]['rangeStart'];?>
</td>
				<td class="type-phone"><?php echo $_smarty_tpl->getVariable('senderID')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sender']['index']]['rangeEnd'];?>
</td>
                                <td class="type-text"><?php echo $_smarty_tpl->getVariable('senderID')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sender']['index']]['cobranderId'];?>
</td>
				<td class="type-status"><?php echo $_smarty_tpl->getVariable('senderID')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sender']['index']]['senderStatusName'];?>
</td>
				<td class="type-action">
					<?php if ($_smarty_tpl->getVariable('senderID')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sender']['index']]['senderEnabled']){?>
					<a href="#" onclick="$app.module('apiuser').disableSender(<?php echo $_smarty_tpl->getVariable('senderID')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sender']['index']]['senderID'];?>
, <?php echo $_smarty_tpl->getVariable('userID')->value;?>
);" class="form-button"><img title="Disable" src="skin/images/icon-disable.png" class="icon-image icon-size-small" alt="" /></a>
					<?php }else{ ?>
					<a href="#" onclick="$app.module('apiuser').enableSender(<?php echo $_smarty_tpl->getVariable('senderID')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sender']['index']]['senderID'];?>
, <?php echo $_smarty_tpl->getVariable('userID')->value;?>
);" class="form-button"><img title="Enable" src="skin/images/icon-enable.png" class="icon-image icon-size-small" alt="" /></a>
					<?php }?>
					<a href="#" onclick="$app.module('apiuser').editSender(<?php echo $_smarty_tpl->getVariable('senderID')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sender']['index']]['senderID'];?>
, <?php echo $_smarty_tpl->getVariable('userID')->value;?>
);" class="form-button"><img title="Edit" src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" /></a>
				</td>
			</tr>
			<?php endfor; endif; ?>
		</tbody>
	</table>
</fieldset>