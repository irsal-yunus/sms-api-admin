<?php /* Smarty version Smarty-3.0.5, created on 2016-09-27 03:36:10
         compiled from "/var/www/html/sms-api-admin/src/templates/client.table.tpl" */ ?>
<?php /*%%SmartyHeaderCode:107901898657da0d28106173-29429530%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7b87251dc44f97d2e32423ab609aeb1179a9eb05' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/client.table.tpl',
      1 => 1474528347,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '107901898657da0d28106173-29429530',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_cycle')) include '/var/www/html/sms-api-admin/src/lib/com/smarty/plugins/function.cycle.php';
?><table class="admin-table">
	<thead>
		<tr>
			<th style="width: 20%;">Company Name</th>
			<th style="width: 15%;">Country</th>
			<th style="width: 20%;">Contact Name</th>
			<th style="width: 15%;">Contact Phone</th>
			<th style="width: 30%;">
				<a href="#" class="form-button" onclick="$app.module('client').createNew();">
					<img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
					<span class="form-button-text">Register</span>
				</a>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="9">&nbsp;</th>
		</tr>
	</tfoot>
	<tbody>
		<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['list']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['name'] = 'list';
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('clients')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
<?php if ($_smarty_tpl->getVariable('clients')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['clientID']==$_smarty_tpl->getVariable('options')->value['highlight']){?> table-row-highlight<?php }?>">
			<td class="type-text"><?php echo $_smarty_tpl->getVariable('clients')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['companyName'];?>
</td>
			<td class="type-text"><?php echo $_smarty_tpl->getVariable('clients')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['countryName'];?>
</td>
			<td class="type-text"><a href="mailto:<?php echo $_smarty_tpl->getVariable('clients')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['contactEmail'];?>
"><?php echo $_smarty_tpl->getVariable('clients')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['contactName'];?>
</a></td>
			<td class="type-phone"><?php echo $_smarty_tpl->getVariable('clients')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['contactPhone'];?>
</td>
			<td class="type-action">
				<a href="#" title="View" class="form-button" onclick="$app.module('client').viewClient(<?php echo $_smarty_tpl->getVariable('clients')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['clientID'];?>
);"><img src="skin/images/icon-view.png" class="icon-image" alt="" /></a>
				<a href="#" title="Edit" class="form-button" onclick="$app.module('client').editClient(<?php echo $_smarty_tpl->getVariable('clients')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['clientID'];?>
);"><img src="skin/images/icon-edit.png" class="icon-image" alt="" /></a>
				<a href="#" title="Delete" class="form-button" onclick="$app.module('client').removeClient(<?php echo $_smarty_tpl->getVariable('clients')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['clientID'];?>
);"><img src="skin/images/icon-remove.png" class="icon-image" alt="" /></a>
				<a href="#" title="Manage Users" class="form-button" onclick="$app.module('client').manageUsers(<?php echo $_smarty_tpl->getVariable('clients')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['clientID'];?>
);"><img src="skin/images/icon-user.png" class="icon-image" alt="" /></a>
                                <a href="#" title="Billing Options" class="form-button" onclick="$app.module('client').smsBilling(<?php echo $_smarty_tpl->getVariable('clients')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['clientID'];?>
);"><img src="skin/images/icon-client.png" class="icon-image" alt="" /></a>
			</td>
		</tr>
		<?php endfor; endif; ?>
	</tbody>
</table>
