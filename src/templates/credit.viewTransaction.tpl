<form class="admin-xform" action="#">
	<fieldset class="float-centre">
		<dl class="admin-definitions">
			<dt>Reference Code</dt><dd><strong>{$details.transactionRef}</strong></dd>
			<dt>Requested By</dt><dd>{$details.transactionRequester}</dd>
			<dt>Credit Mutation</dt><dd>{$details.transactionCredit}</dd>
			<dt>Price</dt><dd>{$details.transactionPrice}</dd>
			<dt>Currency</dt><dd>{$currencyDesc[$details.transactionCurrency]} [<em>{$details.transactionCurrency}</em>]</dd>
			<dt>Payment Method</dt><dd>{if isset($paymentMethods[$details.paymentMethod])}{$paymentMethods[$details.paymentMethod]}{else}{$undefinedMethodDesc}{/if}</dd>
			<dt>Payment Status</dt><dd>{$details.paymentStatusName}</dd>
			<dt>Payment Date</dt><dd>{$details.paymentDate}</dd>
			<dt>Created Date</dt><dd>{$details.transactionCreatedDate}</dd>
			<dt>Created By</dt><dd>{$details.transactionCreatedByName}</dd>
			<dt>Updated Date</dt><dd>{$details.transactionUpdatedDate}</dd>
			<dt>Updated By</dt><dd>{$details.transactionUpdatedByName}</dd>
			<dt>Remark</dt><dd>{$details.transactionRemark}</dd>
		</dl>
	</fieldset>
</form>