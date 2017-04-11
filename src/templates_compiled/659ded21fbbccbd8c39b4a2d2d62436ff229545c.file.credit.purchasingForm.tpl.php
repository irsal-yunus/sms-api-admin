<?php /* Smarty version Smarty-3.0.5, created on 2016-09-27 09:46:24
         compiled from "/var/www/html/sms-api-admin/src/templates/credit.purchasingForm.tpl" */ ?>
<?php /*%%SmartyHeaderCode:135198943857da118b17b631-55688894%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '659ded21fbbccbd8c39b4a2d2d62436ff229545c' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/credit.purchasingForm.tpl',
      1 => 1474528347,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '135198943857da118b17b631-55688894',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_html_options')) include '/var/www/html/sms-api-admin/src/lib/com/smarty/plugins/function.html_options.php';
?><form action="credit.topUp" class="admin-xform">
<fieldset class="float-centre">
<input type="hidden" name="userID" value="<?php echo $_smarty_tpl->getVariable('userID')->value;?>
"/>
	<label class="form-flag-required">Requested By</label><input name="transactionRequester" value="" type="text" maxlength="30" /><span class="ui-helper-clearfix"></span>
	<label class="form-flag-required">Credit</label><input class="type-counter" name="transactionCredit" value="" type="text" maxlength="20"/><span class="ui-helper-clearfix"></span>
	<label class="form-flag-required">Price</label><input class="type-money" name="transactionPrice" value="0" type="text" maxlength="17"/>
	<select name="transactionCurrency" size="1" class="flexible-width">
		<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->getVariable('currencyList')->value,'selected'=>$_smarty_tpl->getVariable('defaultCurrency')->value),$_smarty_tpl);?>

	</select>
	<span class="ui-helper-clearfix"></span>
	<label class="form-flag-required">Payment Method</label>
	<select name="paymentMethod" size="1" class="flexible-width">
		<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->getVariable('paymentMethods')->value,'selected'=>$_smarty_tpl->getVariable('defaultPaymentMethod')->value),$_smarty_tpl);?>

	</select>
	<span class="ui-helper-clearfix"></span>
	<label>Information</label>
	<textarea rows="4" cols="30" style="width:30em;height: 4em;" name="transactionRemark"></textarea>
</fieldset>
</form>