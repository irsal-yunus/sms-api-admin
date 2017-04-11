<?php /* Smarty version Smarty-3.0.5, created on 2016-09-27 03:47:28
         compiled from "/var/www/html/sms-api-admin/src/templates/apiuser.regSenderForm.tpl" */ ?>
<?php /*%%SmartyHeaderCode:30523291157e9ebd072bf73-51850396%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c707872a8b1c4d27641b2c7f51f063e6b882c6f9' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/apiuser.regSenderForm.tpl',
      1 => 1474528347,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '30523291157e9ebd072bf73-51850396',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>

    <script type="text/javascript">
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


<form action="apiuser.addSender" class="admin-xform">
<input value="<?php echo $_smarty_tpl->getVariable('userID')->value;?>
" type="hidden" name="userID"/>
<fieldset class="float-centre">
	<label class="form-flag-required">Sender Name</label><input name="senderName" value="" type="text" maxlength="16"/><span class="ui-helper-clearfix"></span>
	<label>Range Start</label><input name="senderRangeStart" value="" type="text" maxlength="20"/><span class="ui-helper-clearfix"></span>
	<label>Range End</label><input name="senderRangeEnd" value="" type="text" maxlength="20"/><span class="ui-helper-clearfix"></span>
        <label>Cobrander ID</label><input name="cobranderId" id="cobranderId" value="" type="text" maxlength="20" readonly/>
         <a id="btnCobrander" onclick="createDialog();" class="form-button" style="height: auto; margin-left:4px;"> Select</a>
         <a id="btnClearCobrander" onclick="clearDialog();" class="form-button" style="height: auto;">Clear</a>
        <span class="ui-helper-clearfix"></span>
	<label>Activate</label><input name="senderEnabled" value="true" type="checkbox" checked="checked"/><span class="ui-helper-clearfix"></span>
        <span class="ui-helper-clearfix"></span>
</fieldset>
    <span class="ui-helper-clearfix"></span>
    <div style="display: none;" class="containerDialog" title="Select Cobrander Id"></div>   
</form>