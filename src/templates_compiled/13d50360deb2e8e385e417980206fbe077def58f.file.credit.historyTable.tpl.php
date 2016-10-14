<?php /* Smarty version Smarty-3.0.5, created on 2016-09-27 09:46:20
         compiled from "/var/www/html/sms-api-admin/src/templates/credit.historyTable.tpl" */ ?>
<?php /*%%SmartyHeaderCode:209013197257da1188cfa815-90671013%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '13d50360deb2e8e385e417980206fbe077def58f' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/credit.historyTable.tpl',
      1 => 1474528347,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '209013197257da1188cfa815-90671013',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_cycle')) include '/var/www/html/sms-api-admin/src/lib/com/smarty/plugins/function.cycle.php';
?><table class="admin-table">

	<thead>
		<tr>
			<th style="width: 15%;">Fill Date</th>
			<th style="width: 15%;">Reference</th>
			<th style="width: 15%;">Mutation</th>
			<th style="width: 15%;" colspan="2">Value</th>
			<th style="width: 15%;">Payment Date</th>
			<th style="width: 25%;">&nbsp;</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="8">&nbsp;</th>
		</tr>
	</tfoot>
	<tbody>
		<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['list']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['name'] = 'list';
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('history')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
">
			<td class="type-date"><?php echo $_smarty_tpl->getVariable('history')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['transactionCreatedDate'];?>
</td>
			<td class="type-code"><?php echo $_smarty_tpl->getVariable('history')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['transactionRef'];?>
</td>
			<td class="type-counter"><?php echo $_smarty_tpl->getVariable('history')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['transactionCredit'];?>
</td>
			<td class="type-text"><strong><?php echo $_smarty_tpl->getVariable('currencySign')->value[$_smarty_tpl->getVariable('history')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['transactionCurrency']];?>
</strong></td>
			<td class="type-money"><?php echo $_smarty_tpl->getVariable('history')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['transactionPrice'];?>
</td>
			<td class="type-date"><?php echo $_smarty_tpl->getVariable('history')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['paymentDate'];?>
</td>
			<td class="type-action">
				<a href="#" class="form-button" onclick="$app.module('credit').viewTransaction(<?php echo $_smarty_tpl->getVariable('history')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['creditTransactionID'];?>
);" ><img title="View" src="skin/images/icon-view.png" class="icon-image icon-size-small" alt="" /></a>
				<?php if (!$_smarty_tpl->getVariable('history')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['paymentAcknowledged']){?>
				<a href="#" class="form-button" onclick="$app.module('credit').editTransaction(<?php echo $_smarty_tpl->getVariable('history')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['creditTransactionID'];?>
, <?php echo $_smarty_tpl->getVariable('history')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['userID'];?>
);" ><img title="Edit" src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" /></a>
				<a href="#" class="form-button" onclick="$app.module('credit').ackTransaction(<?php echo $_smarty_tpl->getVariable('history')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['creditTransactionID'];?>
, <?php echo $_smarty_tpl->getVariable('history')->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['userID'];?>
);" ><img title="Acknowledge Payment" src="skin/images/icon-ack.png" class="icon-image icon-size-small" alt="" /></a>
				<?php }?>
			</td>
		</tr>
		<?php endfor; endif; ?>
	</tbody>
</table>
