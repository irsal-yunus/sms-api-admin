<form action="client.register" class="admin-xform">
	<fieldset class="float-centre">
		<legend>Company</legend>
		<label class="form-flag-required">Company Name</label><input name="companyName" value="" type="text" maxlength="32"/><span class="ui-helper-clearfix"></span>
		<label>Company URL</label><input name="companyUrl" value="" type="text" maxlength="50"/><span class="ui-helper-clearfix"></span>
		<label class="form-flag-required">Country</label>
		<select name="countryCode" size="1" class="flexible-width">
			{html_options options=$countries selected=$defaultCountryCode}
		</select>
	</fieldset>
	<fieldset class="float-centre">
		<legend>Contact</legend>
		<label class="form-flag-required">Contact Name</label><input name="contactName" value="" type="text" maxlength="32"/><span class="ui-helper-clearfix"></span>
		<label>Contact Email</label><input name="contactEmail" value="" type="text" maxlength="32"/><span class="ui-helper-clearfix"></span>
		<label>Contact Phone</label><input name="contactPhone" value="" type="text" maxlength="32"/><span class="ui-helper-clearfix"></span>
	</fieldset>
</form>