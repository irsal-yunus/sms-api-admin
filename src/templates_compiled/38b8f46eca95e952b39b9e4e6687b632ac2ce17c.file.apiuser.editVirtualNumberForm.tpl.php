<?php /* Smarty version Smarty-3.0.5, created on 2016-09-27 03:53:55
         compiled from "/var/www/html/sms-api-admin/src/templates/apiuser.editVirtualNumberForm.tpl" */ ?>
<?php /*%%SmartyHeaderCode:39426305457e9ed530ec135-46376880%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '38b8f46eca95e952b39b9e4e6687b632ac2ce17c' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/apiuser.editVirtualNumberForm.tpl',
      1 => 1474528347,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '39426305457e9ed530ec135-46376880',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<form action="apiuser.updateVirtualNumber" method="post" class="admin-xform">
<input value="<?php echo $_smarty_tpl->getVariable('details')->value['virtualNumberID'];?>
" type="hidden" name="virtualNumberID"/>
<fieldset style="width:95%;" class="float-centre">
	<label class="form-flag-required">Destination</label><input name="virtualDestination" type="text" value="<?php echo $_smarty_tpl->getVariable('details')->value['virtualDestination'];?>
" maxlength="16"/><span class="ui-helper-clearfix"></span>
	<label>Use Foward URL</label>
		<input name="virtualUrlActive" onclick="if($(this).is(':checked')) $('#apiuser-regvnum-virtualurl').removeAttr('readonly').focus();" value="1" type="radio" <?php if ($_smarty_tpl->getVariable('details')->value['virtualUrlActive']){?>checked="checked"<?php }?>/>
		<label class="flexible-width">Yes</label>
		<input name="virtualUrlActive" onclick="if($(this).is(':checked')) $('#apiuser-regvnum-virtualurl').attr('readonly','readonly').val('');" value="0" type="radio" <?php if (!$_smarty_tpl->getVariable('details')->value['virtualUrlActive']){?>checked="checked"<?php }?>/>
		<label class="flexible-width">No</label>
		<span class="ui-helper-clearfix"></span>
	<label>URL</label><input id="apiuser-regvnum-virtualurl" name="virtualUrl" type="text" value="<?php echo $_smarty_tpl->getVariable('details')->value['virtualUrl'];?>
" <?php if (!$_smarty_tpl->getVariable('details')->value['virtualUrlActive']){?>readonly="readonly"<?php }?>/><span class="ui-helper-clearfix" maxlength="255"></span>
</fieldset>
</form>