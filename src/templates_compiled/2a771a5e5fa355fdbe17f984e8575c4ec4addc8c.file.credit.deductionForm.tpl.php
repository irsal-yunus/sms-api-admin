<?php /* Smarty version Smarty-3.0.5, created on 2016-09-27 09:58:12
         compiled from "/var/www/html/sms-api-admin/src/templates/credit.deductionForm.tpl" */ ?>
<?php /*%%SmartyHeaderCode:204409444757ea42b49f6c00-91716719%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2a771a5e5fa355fdbe17f984e8575c4ec4addc8c' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/credit.deductionForm.tpl',
      1 => 1474528347,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '204409444757ea42b49f6c00-91716719',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<form action="credit.deduct" class="admin-xform">
<input type="hidden" name="userID" value="<?php echo $_smarty_tpl->getVariable('user')->value['userID'];?>
"/>
<fieldset class="float-centre">
	<label class="form-flag-required">Credit</label><input class="type-counter" name="transactionCredit" value="0" type="text" maxlength="30"/><span class="ui-helper-clearfix"></span>
	<label class="form-flag-required">Information</label>
	<textarea rows="4" cols="30" style="width:30em;height: 4em;" name="transactionRemark"></textarea>
</fieldset>
</form>