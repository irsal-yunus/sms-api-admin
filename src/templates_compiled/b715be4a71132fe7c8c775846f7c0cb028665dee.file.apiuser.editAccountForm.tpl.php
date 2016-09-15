<?php /* Smarty version Smarty-3.0.5, created on 2016-08-26 09:49:53
         compiled from "/www/web/sms-api-admin/src/templates/apiuser.editAccountForm.tpl" */ ?>
<?php /*%%SmartyHeaderCode:79955215257c010c196d703-26318520%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b715be4a71132fe7c8c775846f7c0cb028665dee' => 
    array (
      0 => '/www/web/sms-api-admin/src/templates/apiuser.editAccountForm.tpl',
      1 => 1472027728,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '79955215257c010c196d703-26318520',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_html_options')) include '/www/web/sms-api-admin/src/lib/com/smarty/plugins/function.html_options.php';
?><script type="text/javascript">
    
        function createDialog() {
            $app.module('apiuser').selectCobranderID();

            $('.containerDialog').dialog({
                width: 450,
                heigth: 900,
                modal: true,
                buttons: {"Close": function() {
                        $(this).dialog("close");
                    }},
                close: function(ev, ui) {
                    $(this).remove();
                }

    });
            $('.containerDialog').dialog("open");

        }
        function clearDialog() {
            var txtCobrander = $('#cobranderId').val();
            if(txtCobrander.length > 0) {
                $('#cobranderId').val('');
            }
        }
    
</script>
<form action="apiuser.updateAccount" class="admin-xform">
	<input type="hidden" name="userID" value="<?php echo $_smarty_tpl->getVariable('userID')->value;?>
">
	<fieldset style="width:95%" class="float-centre">
                
                <label class="form-flag-required">Client Company</label>
                <select name="clientID" size="1" class="flexible-width">
				<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->getVariable('clientList')->value,'selected'=>$_smarty_tpl->getVariable('details')->value['clientID']),$_smarty_tpl);?>

			</select>
                
               <span class="ui-helper-clearfix"></span>
                
		<label class="form-flag-required">User Name</label><input name="userName" value="<?php echo $_smarty_tpl->getVariable('details')->value['userName'];?>
" type="text"/><span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">Cobrander ID (User)</label><input name="cobranderID" id="cobranderId" value="<?php echo $_smarty_tpl->getVariable('details')->value['cobranderID'];?>
" type="text" readonly/>
                <a id="btnCobrander"onclick="createDialog();" class="form-button" style="height: auto; margin-left:4px;" >Select</a>
                <a id="btnClearCobrander" onclick="clearDialog();" class="form-button" style="height: auto;">Clear</a>
        <span class="ui-helper-clearfix"></span>
		<?php if ($_smarty_tpl->getVariable('details')->value['replyBlacklistEnabled']){?>
		<label class="form-flag-required">Use Blacklist</label><input name="replyBlacklistEnabled" value="true" type="checkbox" checked="checked"/><span class="ui-helper-clearfix"></span>
		<?php }else{ ?>
		<label class="form-flag-required">Use Blacklist</label><input name="replyBlacklistEnabled" value="true" type="checkbox"/><span class="ui-helper-clearfix"></span>
		<?php }?>
		<label>Status Delivery</label>
			<input name="statusDeliveryActive" value="1" type="radio" <?php if ($_smarty_tpl->getVariable('details')->value['statusDeliveryActive']){?>checked="checked" <?php }?>onclick="if($(this).is(':checked')) $('#apiuser-editaccform-deliveryurl').removeAttr('readonly');" />
			<label class="flexible-width">Yes</label>
			<input name="statusDeliveryActive" value="0" type="radio" <?php if (!$_smarty_tpl->getVariable('details')->value['statusDeliveryActive']){?>checked="checked" <?php }?>onclick="if($(this).is(':checked')) $('#apiuser-editaccform-deliveryurl').val('').attr('readonly', 'readonly');" />
			<label class="flexible-width">No</label>
			<span class="ui-helper-clearfix"></span>
			<label>Delivery URL</label><input id="apiuser-editaccform-deliveryurl" name="statusDeliveryUrl" value="<?php echo $_smarty_tpl->getVariable('details')->value['statusDeliveryUrl'];?>
" type="text" style="width: 20em;" <?php if (!$_smarty_tpl->getVariable('details')->value['statusDeliveryActive']){?> readonly="readonly"<?php }?>/>
		<span class="ui-helper-clearfix"></span>
		<label>Is Postpaid</label>
			<input name="isPostpaid" value="1" type="radio" <?php if ($_smarty_tpl->getVariable('details')->value['isPostpaid']){?>checked="checked" <?php }?>/>
			<label class="flexible-width">Yes</label>
			<input name="isPostpaid" value="0" type="radio" <?php if (!$_smarty_tpl->getVariable('details')->value['isPostpaid']){?>checked="checked" <?php }?>/>
			<label class="flexible-width">No</label>
			<span class="ui-helper-clearfix"></span>  
        <span class="ui-helper-clearfix"></span>
	</fieldset>
    <div style="display: none;" class="containerDialog" title="Select Cobrander Id"></div>
</form>