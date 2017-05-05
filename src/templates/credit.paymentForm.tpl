<form action="credit.ackTransaction" class="admin-xform">
<input type="hidden" name="creditTransactionID" value="{$transaction.creditTransactionID}"/>
<fieldset class="float-centre">
	<label>Reference</label><input type="text" value="{$transaction.transactionRef}" disabled="disabled" /><span class="ui-helper-clearfix"></span>
	<label>Requested By</label><input name="transactionRequester" value="{$transaction.transactionRequester}" type="text" disabled="disabled"/><span class="ui-helper-clearfix"></span>
	<label>Credit</label><input type="text" value="{$transaction.transactionCredit}" disabled="disabled" class="type-counter"/><span class="ui-helper-clearfix"></span>
	<label>Price</label><input type="text" name="transactionPrice" value="{$transaction.transactionPrice}" disabled="disabled" class="type-money"/><span class="ui-helper-clearfix"></span>
	<label>Currency</label>
	<select size="1" class="flexible-width" disabled="disabled">
		{html_options options=$currencyList selected=$transaction.transactionCurrency}
	</select>
	<span class="ui-helper-clearfix"></span>
	<label>Payment Method</label>
	<select size="1" class="flexible-width" disabled="disabled">
		{html_options options=$paymentMethods selected=$transaction.paymentMethod}
	</select>
	<span class="ui-helper-clearfix"></span>
	<label class="form-flag-required">Payment Date</label><input type="text" name="paymentDate" value="{$transaction.paymentDate}" class="form-datepicker" maxlength="10"/><span class="ui-helper-clearfix"></span>
</fieldset>
</form>