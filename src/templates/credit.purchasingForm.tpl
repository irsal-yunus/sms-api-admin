<form action="credit.topUp" class="admin-xform">
<fieldset class="float-centre">
<input type="hidden" name="userID" value="{$userID}"/>
	<label class="form-flag-required">Requested By</label><input name="transactionRequester" value="" type="text" maxlength="30" /><span class="ui-helper-clearfix"></span>
	<label class="form-flag-required">Credit</label><input class="type-counter" name="transactionCredit" value="" type="text" maxlength="20"/><span class="ui-helper-clearfix"></span>
	<label class="form-flag-required">Price</label><input class="type-money" name="transactionPrice" value="0" type="text" maxlength="17"/>
	<select name="transactionCurrency" size="1" class="flexible-width">
		{html_options options=$currencyList selected=$defaultCurrency}
	</select>
	<span class="ui-helper-clearfix"></span>
	<label class="form-flag-required">Payment Method</label>
	<select name="paymentMethod" size="1" class="flexible-width">
		{html_options options=$paymentMethods selected=$defaultPaymentMethod}
	</select>
	<span class="ui-helper-clearfix"></span>
	<label>Information</label>
	<textarea rows="4" cols="30" style="width:30em;height: 4em;" name="transactionRemark"></textarea>
</fieldset>
</form>