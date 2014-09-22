<table class="admin-table">

	<thead>
		<tr>
			<th style="width: 15%;">Fill Date</th>
			<th style="width: 15%;">Reference</th>
			<th style="width: 15%;">Mutation</th>
			<th style="width: 15%;" colspan="2">Value</th>
			<th style="width: 15%;">Payment Date</th>
			<th style="width: 25%;">&nbsp;</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="8">&nbsp;</th>
		</tr>
	</tfoot>
	<tbody>
		{section name=list loop=$history}
		<tr class="{cycle values="zebra-odd,zebra-even"}">
			<td class="type-date">{$history[list].transactionCreatedDate}</td>
			<td class="type-code">{$history[list].transactionRef}</td>
			<td class="type-counter">{$history[list].transactionCredit}</td>
			<td class="type-text"><strong>{$currencySign[$history[list].transactionCurrency]}</strong></td>
			<td class="type-money">{$history[list].transactionPrice}</td>
			<td class="type-date">{$history[list].paymentDate}</td>
			<td class="type-action">
				<a href="#" class="form-button" onclick="$app.module('credit').viewTransaction({$history[list].creditTransactionID});" ><img title="View" src="skin/images/icon-view.png" class="icon-image icon-size-small" alt="" /></a>
				{if !$history[list].paymentAcknowledged}
				<a href="#" class="form-button" onclick="$app.module('credit').editTransaction({$history[list].creditTransactionID}, {$history[list].userID});" ><img title="Edit" src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" /></a>
				<a href="#" class="form-button" onclick="$app.module('credit').ackTransaction({$history[list].creditTransactionID}, {$history[list].userID});" ><img title="Acknowledge Payment" src="skin/images/icon-ack.png" class="icon-image icon-size-small" alt="" /></a>
				{/if}
			</td>
		</tr>
		{/section}
	</tbody>
</table>
