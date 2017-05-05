<?php /* Smarty version Smarty-3.0.5, created on 2016-10-18 11:59:03
         compiled from "/var/www/html/sms-api-admin-61/src/templates/apiuser.regSenderForm.tpl" */ ?>
<?php /*%%SmartyHeaderCode:98918588458060e8761d8c4-39970664%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f1534768042fae4200000fa316412ec51f88b606' => 
    array (
      0 => '/var/www/html/sms-api-admin-61/src/templates/apiuser.regSenderForm.tpl',
      1 => 1476791134,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '98918588458060e8761d8c4-39970664',
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