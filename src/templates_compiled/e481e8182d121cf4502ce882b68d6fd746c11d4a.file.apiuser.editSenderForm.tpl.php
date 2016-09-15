<?php /* Smarty version Smarty-3.0.5, created on 2016-09-14 05:03:40
         compiled from "/var/www/html/sms-api-admin/src/templates/apiuser.editSenderForm.tpl" */ ?>
<?php /*%%SmartyHeaderCode:67344954757d8da2c2a39f3-64318234%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e481e8182d121cf4502ce882b68d6fd746c11d4a' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/apiuser.editSenderForm.tpl',
      1 => 1473742354,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '67344954757d8da2c2a39f3-64318234',
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
                buttons: {"Close": function () {
                        $(this).dialog("close");
                    }},
                close: function (ev, ui) {
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


<form action="apiuser.updateSender" class="admin-xform">
<input value="<?php echo $_smarty_tpl->getVariable('senderID')->value;?>
" type="hidden" name="senderID"/>
    <input value="<?php echo $_smarty_tpl->getVariable('userID')->value;?>
" type="hidden" name="useID"/>
<fieldset class="float-centre">
	<label class="form-flag-required">Sender Name</label><input name="senderName" value="<?php echo $_smarty_tpl->getVariable('senderName')->value;?>
" type="text" maxlength="16"/><span class="ui-helper-clearfix"></span>
	<label>Range Start</label><input name="senderRangeStart" value="<?php echo $_smarty_tpl->getVariable('senderRangeStart')->value;?>
" type="text" maxlength="20"/><span class="ui-helper-clearfix"></span>
	<label>Range End</label><input name="senderRangeEnd" value="<?php echo $_smarty_tpl->getVariable('senderRangeEnd')->value;?>
" type="text" maxlength="20"/><span class="ui-helper-clearfix"></span>
        <label>Cobrander ID</label><input id="cobranderId" name="cobranderID" value="<?php echo $_smarty_tpl->getVariable('cobranderID')->value;?>
" type="text" maxlength="20"readonly/>
        <a id="btnCobrander" onclick="createDialog();" class="form-button" style="height: auto; margin-left:4px;">Select</a>
        <a id="btnClearCobrander" onclick="clearDialog();" class="form-button" style="height: auto;">Clear</a>
        <span class="ui-helper-clearfix"></span>
</fieldset>
    <span class="ui-helper-clearfix"></span>
    <div style="display: none;" class="containerDialog" title="Select Cobrander Id"></div>
</form>