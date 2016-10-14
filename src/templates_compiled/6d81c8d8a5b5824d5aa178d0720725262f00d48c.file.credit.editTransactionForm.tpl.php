<?php /* Smarty version Smarty-3.0.5, created on 2016-09-27 09:59:03
         compiled from "/var/www/html/sms-api-admin/src/templates/credit.editTransactionForm.tpl" */ ?>
<?php /*%%SmartyHeaderCode:125725018257ea42e73aaef9-22816326%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6d81c8d8a5b5824d5aa178d0720725262f00d48c' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/credit.editTransactionForm.tpl',
      1 => 1474528347,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '125725018257ea42e73aaef9-22816326',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_html_options')) include '/var/www/html/sms-api-admin/src/lib/com/smarty/plugins/function.html_options.php';
?><form action="credit.updateTransactionDetails" class="admin-xform">
<input type="hidden" name="creditTransactionID" value="<?php echo $_smarty_tpl->getVariable('transaction')->value['creditTransactionID'];?>
"/>
<fieldset class="float-centre">
	<label>Transaction Ref</label><input value="<?php echo $_smarty_tpl->getVariable('transaction')->value['transactionRef'];?>
" type="text" disabled="disabled"/><span class="ui-helper-clearfix"></span>
	<label class="form-flag-required">Requested By</label><input name="transactionRequester" value="<?php echo $_smarty_tpl->getVariable('transaction')->value['transactionRequester'];?>
" type="text" maxlength="30"/><span class="ui-helper-clearfix"></span>
	<label>Credit</label><input class="type-counter" value="<?php echo $_smarty_tpl->getVariable('transaction')->value['transactionCredit'];?>
" type="text" disabled="disabled"/><span class="ui-helper-clearfix"></span>
	<label class="form-flag-required">Price</label><input class="type-money" name="transactionPrice" value="<?php echo $_smarty_tpl->getVariable('transaction')->value['transactionPrice'];?>
" type="text" maxlength="17"/>
	<select name="transactionCurrency" size="1" class="flexible-width">
		<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->getVariable('currencyList')->value,'selected'=>$_smarty_tpl->getVariable('transaction')->value['transactionCurrency']),$_smarty_tpl);?>

	</select>
	<span class="ui-helper-clearfix"></span>
	<label class="form-flag-required">Payment Method</label>
	<select name="paymentMethod" size="1" class="flexible-width">
		<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->getVariable('paymentMethods')->value,'selected'=>$_smarty_tpl->getVariable('transaction')->value['paymentMethod']),$_smarty_tpl);?>

	</select>
	<span class="ui-helper-clearfix"></span>
	<label>Information</label>
	<textarea rows="4" cols="30" style="width:30em;height: 4em;" name="transactionRemark"><?php echo $_smarty_tpl->getVariable('transaction')->value['transactionRemark'];?>
</textarea>
</fieldset>
</form>