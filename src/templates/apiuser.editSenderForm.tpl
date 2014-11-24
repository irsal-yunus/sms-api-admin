{literal}
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
{/literal}

<form action="apiuser.updateSender" class="admin-xform">
<input value="{$senderID}" type="hidden" name="senderID"/>
    <input value="{$userID}" type="hidden" name="useID"/>
<fieldset class="float-centre">
	<label class="form-flag-required">Sender Name</label><input name="senderName" value="{$senderName}" type="text" maxlength="16"/><span class="ui-helper-clearfix"></span>
	<label>Range Start</label><input name="senderRangeStart" value="{$senderRangeStart}" type="text" maxlength="20"/><span class="ui-helper-clearfix"></span>
	<label>Range End</label><input name="senderRangeEnd" value="{$senderRangeEnd}" type="text" maxlength="20"/><span class="ui-helper-clearfix"></span>
        <label>Cobrander ID</label><input id="cobranderId" name="cobranderID" value="{$cobranderID}" type="text" maxlength="20"readonly/>
        <a id="btnCobrander" onclick="createDialog();" class="form-button" style="height: auto; margin-left:4px;">Select</a>
        <a id="btnClearCobrander" onclick="clearDialog();" class="form-button" style="height: auto;">Clear</a>
        <span class="ui-helper-clearfix"></span>
</fieldset>
    <span class="ui-helper-clearfix"></span>
    <div style="display: none;" class="containerDialog" title="Select Cobrander Id"></div>
</form>