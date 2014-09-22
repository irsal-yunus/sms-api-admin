<form action="apiuser.addVirtualNumber" method="post" class="admin-xform">
<input value="{$userID}" type="hidden" name="userID"/>
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