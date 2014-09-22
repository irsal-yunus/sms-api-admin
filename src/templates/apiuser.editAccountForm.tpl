<script type="text/javascript">
    $(function($) { 
            $("#apiuser-regform-expired-date").datepicker();
    });
</script>
<form action="apiuser.updateAccount" class="admin-xform">
	<input type="hidden" name="userID" value="{$userID}">
	<fieldset style="width:95%" class="float-centre">
		<label class="form-flag-required">Client Company</label><label class="flexible-width">{$details.clientCompanyName}</label><span class="ui-helper-clearfix"></span>
		<label class="form-flag-required">User Name</label><input name="userName" value="{$details.userName}" type="text"/><span class="ui-helper-clearfix"></span>
		<label class="form-flag-required">Cobrander ID</label><input name="cobranderID" value="{$details.cobranderID}" type="text"/><span class="ui-helper-clearfix"></span>
		{if $details.replyBlacklistEnabled}
		<label class="form-flag-required">Use Blacklist</label><input name="replyBlacklistEnabled" value="true" type="checkbox" checked="checked"/><span class="ui-helper-clearfix"></span>
		{else}
		<label class="form-flag-required">Use Blacklist</label><input name="replyBlacklistEnabled" value="true" type="checkbox"/><span class="ui-helper-clearfix"></span>
		{/if}
		<label>Status Delivery</label>
			<input name="statusDeliveryActive" value="1" type="radio" {if $details.statusDeliveryActive}checked="checked" {/if}onclick="if($(this).is(':checked')) $('#apiuser-editaccform-deliveryurl').removeAttr('readonly');" />
			<label class="flexible-width">Yes</label>
			<input name="statusDeliveryActive" value="0" type="radio" {if !$details.statusDeliveryActive}checked="checked" {/if}onclick="if($(this).is(':checked')) $('#apiuser-editaccform-deliveryurl').val('').attr('readonly', 'readonly');" />
			<label class="flexible-width">No</label>
			<span class="ui-helper-clearfix"></span>
			<label>Delivery URL</label><input id="apiuser-editaccform-deliveryurl" name="statusDeliveryUrl" value="{$details.statusDeliveryUrl}" type="text" style="width: 20em;" {if !$details.statusDeliveryActive}readonly="readonly"{/if}/>
		<span class="ui-helper-clearfix"></span>
		<label>Is Postpaid</label>
			<input name="isPostpaid" value="1" type="radio" {if $details.isPostpaid}checked="checked" {/if}/>
			<label class="flexible-width">Yes</label>
			<input name="isPostpaid" value="0" type="radio" {if !$details.isPostpaid}checked="checked" {/if}/>
			<label class="flexible-width">No</label>
			<span class="ui-helper-clearfix"></span>
               <label>Expired Date</label><input id="apiuser-regform-expired-date" name="expiredDate" value="{$details.expiredDate}" type="text" style="width: 11em;"/>                 
	</fieldset>

</form>