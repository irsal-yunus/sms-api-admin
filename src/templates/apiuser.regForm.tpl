{literal}
<script type="text/javascript"> 
    $(function() {
            $("#apiuser-regform-expired-date").datepicker();
    });
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
{/literal}

<form action="apiuser.register" class="admin-xform" >
	<fieldset style="width:95%" class="float-centre">
		{if $clientLock}
			<input type="hidden" name="clientID" value="{$clientID}">
			<label class="form-flag-required">Client Company</label><label class="flexible-width">{$companyName}</label><span class="ui-helper-clearfix"></span>
		{else}
			<label>Client</label>
			<select name="clientID" size="1" class="flexible-width">
				{html_options options=$clientList}
			</select>
		{/if}
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
                <label>Expired Date</label><input id="apiuser-regform-expired-date" name="expiredDate" value="" type="text" style="width: 11em;"/>        
	</fieldset>
	<span class="ui-helper-clearfix"></span>
        <div style="display: none;" class="containerDialog" title="Select Cobrander Id"></div>
</form>