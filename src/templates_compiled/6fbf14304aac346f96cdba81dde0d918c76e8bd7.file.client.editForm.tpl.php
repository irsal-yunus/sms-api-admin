<?php /* Smarty version Smarty-3.0.5, created on 2016-08-30 13:00:51
         compiled from "/www/web/sms-api-admin/src/templates/client.editForm.tpl" */ ?>
<?php /*%%SmartyHeaderCode:117041939257c58383401cc1-46543730%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6fbf14304aac346f96cdba81dde0d918c76e8bd7' => 
    array (
      0 => '/www/web/sms-api-admin/src/templates/client.editForm.tpl',
      1 => 1472027728,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '117041939257c58383401cc1-46543730',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_html_options')) include '/www/web/sms-api-admin/src/lib/com/smarty/plugins/function.html_options.php';
?><form action="client.update" class="admin-xform">
	<input type="hidden" value="<?php echo $_smarty_tpl->getVariable('client')->value['clientID'];?>
" name="clientID"/>
	<fieldset class="float-centre">
		<legend>Company</legend>
		<label class="form-flag-required">Company Name</label><input name="companyName" value="<?php echo $_smarty_tpl->getVariable('client')->value['companyName'];?>
" type="text" maxlength="32"/><span class="ui-helper-clearfix"></span>
		<label>Company URL</label><input name="companyUrl" value="<?php echo $_smarty_tpl->getVariable('client')->value['companyUrl'];?>
" type="text" maxlength="50"/><span class="ui-helper-clearfix"></span>
		<label class="form-flag-required">Country</label>
		<select name="countryCode" size="1" class="flexible-width">
			<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->getVariable('countries')->value,'selected'=>$_smarty_tpl->getVariable('client')->value['countryCode']),$_smarty_tpl);?>

		</select>
	</fieldset>
	<fieldset  class="float-centre">
		<legend>Contact</legend>
		<label class="form-flag-required">Contact Name</label><input name="contactName" value="<?php echo $_smarty_tpl->getVariable('client')->value['contactName'];?>
" type="text" maxlength="32"/><span class="ui-helper-clearfix"></span>
		<label>Contact Email</label><input name="contactEmail" value="<?php echo $_smarty_tpl->getVariable('client')->value['contactEmail'];?>
" type="text" maxlength="32"/><span class="ui-helper-clearfix"></span>
		<label>Contact Phone</label><input name="contactPhone" value="<?php echo $_smarty_tpl->getVariable('client')->value['contactPhone'];?>
" type="text" maxlength="32"/><span class="ui-helper-clearfix"></span>
	</fieldset>
</form>