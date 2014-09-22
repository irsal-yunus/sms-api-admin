<form action="apiuser.updateSender" class="admin-xform">
<input value="{$senderID}" type="hidden" name="senderID"/>
<fieldset class="float-centre">
	<label class="form-flag-required">Sender Name</label><input name="senderName" value="{$senderName}" type="text" maxlength="16"/><span class="ui-helper-clearfix"></span>
	<label>Range Start</label><input name="senderRangeStart" value="{$senderRangeStart}" type="text" maxlength="20"/><span class="ui-helper-clearfix"></span>
	<label>Range End</label><input name="senderRangeEnd" value="{$senderRangeEnd}" type="text" maxlength="20"/><span class="ui-helper-clearfix"></span>
</fieldset>
</form>