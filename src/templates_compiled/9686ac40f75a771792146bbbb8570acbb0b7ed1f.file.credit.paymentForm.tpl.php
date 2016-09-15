<?php /* Smarty version Smarty-3.0.5, created on 2016-09-14 05:05:34
         compiled from "/var/www/html/sms-api-admin/src/templates/credit.paymentForm.tpl" */ ?>
<?php /*%%SmartyHeaderCode:188582418457d8da9e5bff08-89099082%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9686ac40f75a771792146bbbb8570acbb0b7ed1f' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/credit.paymentForm.tpl',
      1 => 1473742353,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '188582418457d8da9e5bff08-89099082',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_html_options')) include '/var/www/html/sms-api-admin/src/lib/com/smarty/plugins/function.html_options.php';
?><form action="credit.ackTransaction" class="admin-xform">
<input type="hidden" name="creditTransactionID" value="<?php echo $_smarty_tpl->getVariable('transaction')->value['creditTransactionID'];?>
"/>
<fieldset class="float-centre">
	<label>Reference</label><input type="text" value="<?php echo $_smarty_tpl->getVariable('transaction')->value['transactionRef'];?>
" disabled="disabled" /><span class="ui-helper-clearfix"></span>
	<label>Requested By</label><input name="transactionRequester" value="<?php echo $_smarty_tpl->getVariable('transaction')->value['transactionRequester'];?>
" type="text" disabled="disabled"/><span class="ui-helper-clearfix"></span>
	<label>Credit</label><input type="text" value="<?php echo $_smarty_tpl->getVariable('transaction')->value['transactionCredit'];?>
" disabled="disabled" class="type-counter"/><span class="ui-helper-clearfix"></span>
	<label>Price</label><input type="text" name="transactionPrice" value="<?php echo $_smarty_tpl->getVariable('transaction')->value['transactionPrice'];?>
" disabled="disabled" class="type-money"/><span class="ui-helper-clearfix"></span>
	<label>Currency</label>
	<select size="1" class="flexible-width" disabled="disabled">
		<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->getVariable('currencyList')->value,'selected'=>$_smarty_tpl->getVariable('transaction')->value['transactionCurrency']),$_smarty_tpl);?>

	</select>
	<span class="ui-helper-clearfix"></span>
	<label>Payment Method</label>
	<select size="1" class="flexible-width" disabled="disabled">
		<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->getVariable('paymentMethods')->value,'selected'=>$_smarty_tpl->getVariable('transaction')->value['paymentMethod']),$_smarty_tpl);?>

	</select>
	<span class="ui-helper-clearfix"></span>
	<label class="form-flag-required">Payment Date</label><input type="text" name="paymentDate" value="<?php echo $_smarty_tpl->getVariable('transaction')->value['paymentDate'];?>
" class="form-datepicker" maxlength="10"/><span class="ui-helper-clearfix"></span>
</fieldset>
</form>