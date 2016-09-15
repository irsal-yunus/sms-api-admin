<?php /* Smarty version Smarty-3.0.5, created on 2016-09-14 04:57:21
         compiled from "/var/www/html/sms-api-admin/src/templates/client.regForm.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16824527557d8d8b16397e4-17080733%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f1093f89ce0ba6aec12ead40f33a9437e1c40635' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/client.regForm.tpl',
      1 => 1473742354,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16824527557d8d8b16397e4-17080733',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_html_options')) include '/var/www/html/sms-api-admin/src/lib/com/smarty/plugins/function.html_options.php';
?><form action="client.register" class="admin-xform">
	<fieldset class="float-centre">
		<legend>Company</legend>
		<label class="form-flag-required">Company Name</label><input name="companyName" value="" type="text" maxlength="32"/><span class="ui-helper-clearfix"></span>
		<label>Company URL</label><input name="companyUrl" value="" type="text" maxlength="50"/><span class="ui-helper-clearfix"></span>
		<label class="form-flag-required">Country</label>
		<select name="countryCode" size="1" class="flexible-width">
			<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->getVariable('countries')->value,'selected'=>$_smarty_tpl->getVariable('defaultCountryCode')->value),$_smarty_tpl);?>

		</select>
	</fieldset>
	<fieldset class="float-centre">
		<legend>Contact</legend>
		<label class="form-flag-required">Contact Name</label><input name="contactName" value="" type="text" maxlength="32"/><span class="ui-helper-clearfix"></span>
		<label>Contact Email</label><input name="contactEmail" value="" type="text" maxlength="32"/><span class="ui-helper-clearfix"></span>
		<label>Contact Phone</label><input name="contactPhone" value="" type="text" maxlength="32"/><span class="ui-helper-clearfix"></span>
	</fieldset>
</form>