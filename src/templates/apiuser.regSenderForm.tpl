<form action="apiuser.addSender" class="admin-xform">
<input value="{$userID}" type="hidden" name="userID"/>
<fieldset class="float-centre">
	<label class="form-flag-required">Sender Name</label><input name="senderName" value="" type="text" maxlength="16"/><span class="ui-helper-clearfix"></span>
	<label>Range Start</label><input name="senderRangeStart" value="" type="text" maxlength="20"/><span class="ui-helper-clearfix"></span>
	<label>Range End</label><input name="senderRangeEnd" value="" type="text" maxlength="20"/><span class="ui-helper-clearfix"></span>
	<label>Activate</label><input name="senderEnabled" value="true" type="checkbox" checked="checked"/><span class="ui-helper-clearfix"></span>
</fieldset>
</form>