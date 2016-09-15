<?php /* Smarty version Smarty-3.0.5, created on 2016-09-14 04:57:06
         compiled from "/var/www/html/sms-api-admin/src/templates/apiuser.regForm.tpl" */ ?>
<?php /*%%SmartyHeaderCode:104971676057d8d8a23f8c82-86682437%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '073fa1f7d38bda4a3017c43348ffd3fffac7388b' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/apiuser.regForm.tpl',
      1 => 1473742354,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '104971676057d8d8a23f8c82-86682437',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_html_options')) include '/var/www/html/sms-api-admin/src/lib/com/smarty/plugins/function.html_options.php';
?>
<script type="text/javascript"> 
//    $(function() {
//            $("#apiuser-regform-expired-date").datepicker();
//    });
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


<form action="apiuser.register" class="admin-xform" >
	<fieldset style="width:95%" class="float-centre">
		<?php if ($_smarty_tpl->getVariable('clientLock')->value){?>
			<input type="hidden" name="clientID" value="<?php echo $_smarty_tpl->getVariable('clientID')->value;?>
">
			<label class="form-flag-required">Client Company</label><label class="flexible-width"><?php echo $_smarty_tpl->getVariable('companyName')->value;?>
</label><span class="ui-helper-clearfix"></span>
		<?php }else{ ?>
			<label>Client</label>
			<select name="clientID" size="1" class="flexible-width">
				<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->getVariable('clientList')->value),$_smarty_tpl);?>

			</select>
		<?php }?>
		<span class="ui-helper-clearfix"></span>
		<label class="form-flag-required">User Name</label><input name="userName" value="" type="text" maxlength="32"/><span class="ui-helper-clearfix"></span>
		<label class="form-flag-required">Password</label><input name="userPassword" value="" type="text"/><span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">Cobrander ID</label><input name="cobranderID" id="cobranderId" value="" type="text" maxlength="16" readonly/> 
                <a id="btnCobrander" onclick="createDialog();" class="form-button" style="height: auto; margin-left:4px;" >Select</a>
                <a id="btnClearCobrander" onclick="clearDialog();" class="form-button" style="height: auto;">Clear</a>
        <span class="ui-helper-clearfix"></span>
		<label>Activate</label><input name="active" value="true" type="checkbox" checked="checked" /><span class="ui-helper-clearfix"></span>
		<label>Status Delivery</label>
			<input name="statusDeliveryActive" value="1" type="radio" onclick="if($(this).is(':checked')) $('#apiuser-regform-deliveryurl').removeAttr('readonly');" />
			<label class="flexible-width">Yes</label>
			<input name="statusDeliveryActive" value="0" type="radio" checked="checked" onclick="if($(this).is(':checked')) $('#apiuser-regform-deliveryurl').val('').attr('readonly', 'readonly');" />
			<label class="flexible-width">No</label>
			<span class="ui-helper-clearfix"></span>
		<label>Delivery URL</label><input id="apiuser-regform-deliveryurl" name="statusDeliveryUrl" value="" type="text" style="width: 20em;" readonly="readonly"/>
		<span class="ui-helper-clearfix"></span>
		<label>Is Postpaid</label>
			<input name="isPostpaid" value="1" type="radio" />
			<label class="flexible-width">Yes</label>
			<input name="isPostpaid" value="0" type="radio" checked="checked" />
			<label class="flexible-width">No</label>
			<span class="ui-helper-clearfix"></span>
               <!-- <label>Expired Date</label><input id="apiuser-regform-expired-date" name="expiredDate" value="" type="text" style="width: 11em;"/> -->       
	</fieldset>
	<span class="ui-helper-clearfix"></span>
        <div style="display: none;" class="containerDialog" title="Select Cobrander Id"></div>
</form>