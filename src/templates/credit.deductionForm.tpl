<form action="credit.deduct" class="admin-xform">
<input type="hidden" name="userID" value="{$user.userID}"/>
<fieldset class="float-centre">
	<label class="form-flag-required">Credit</label><input class="type-counter" name="transactionCredit" value="0" type="text" maxlength="30"/><span class="ui-helper-clearfix"></span>
	<label class="form-flag-required">Information</label>
	<textarea rows="4" cols="30" style="width:30em;height: 4em;" name="transactionRemark"></textarea>
</fieldset>
</form>