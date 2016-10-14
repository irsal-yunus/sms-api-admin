<?php /* Smarty version Smarty-3.0.5, created on 2016-09-27 03:51:58
         compiled from "/var/www/html/sms-api-admin/src/templates/apiuser.regVirtualNumber.tpl" */ ?>
<?php /*%%SmartyHeaderCode:28013641657d8d49a105996-11105889%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a3c75a7237806b510518e7d6beecd7601bd12db4' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/apiuser.regVirtualNumber.tpl',
      1 => 1474528347,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28013641657d8d49a105996-11105889',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<form action="apiuser.addVirtualNumber" method="post" class="admin-xform">
<input value="<?php echo $_smarty_tpl->getVariable('userID')->value;?>
" type="hidden" name="userID"/>
<fieldset style="width:95%;" class="float-centre">
	<label class="form-flag-required">Destination</label><input name="virtualDestination" type="text" maxlength="16"/><span class="ui-helper-clearfix"></span>
	<label>Use Foward URL</label>
		<input name="virtualUrlActive" onclick="if($(this).is(':checked')) $('#apiuser-regvnum-virtualurl').removeAttr('readonly').focus();" value="1" type="radio" />
		<label class="flexible-width">Yes</label>
		<input name="virtualUrlActive" onclick="if($(this).is(':checked')) $('#apiuser-regvnum-virtualurl').attr('readonly','readonly').val('');" value="0" type="radio" checked="checked"/>
		<label class="flexible-width">No</label>
		<span class="ui-helper-clearfix"></span>
	<label>URL</label><input id="apiuser-regvnum-virtualurl" name="virtualUrl" type="text" value="" readonly="readonly" maxlength="255"/><span class="ui-helper-clearfix"></span>
</fieldset>
</form>