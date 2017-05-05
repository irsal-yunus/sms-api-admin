<form action="credit.updateTransactionDetails" class="admin-xform">
<input type="hidden" name="creditTransactionID" value="{$transaction.creditTransactionID}"/>
<fieldset class="float-centre">
	<label>Transaction Ref</label><input value="{$transaction.transactionRef}" type="text" disabled="disabled"/><span class="ui-helper-clearfix"></span>
	<label class="form-flag-required">Requested By</label><input name="transactionRequester" value="{$transaction.transactionRequester}" type="text" maxlength="30"/><span class="ui-helper-clearfix"></span>
	<label>Credit</label><input class="type-counter" value="{$transaction.transactionCredit}" type="text" disabled="disabled"/><span class="ui-helper-clearfix"></span>
	<label class="form-flag-required">Price</label><input class="type-money" name="transactionPrice" value="{$transaction.transactionPrice}" type="text" maxlength="17"/>
	<select name="transactionCurrency" size="1" class="flexible-width">
		{html_options options=$currencyList selected=$transaction.transactionCurrency}
	</select>
	<span class="ui-helper-clearfix"></span>
	<label class="form-flag-required">Payment Method</label>
	<select name="paymentMethod" size="1" class="flexible-width">
		{html_options options=$paymentMethods selected=$transaction.paymentMethod}
	</select>
	<span class="ui-helper-clearfix"></span>
	<label>Information</label>
	<textarea rows="4" cols="30" style="width:30em;height: 4em;" name="transactionRemark">{$transaction.transactionRemark}</textarea>
</fieldset>
</form>