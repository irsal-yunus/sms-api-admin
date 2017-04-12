<fieldset>
	<table class="admin-simpletable">
		<thead>
			<tr>
				<th class="zebra-odd" style="width:65%;">Blacklisted MSISDN</th>
				<th class="zebra-even" style="width:35%;">
					<a href="#" onclick="$app.module('apiuser').blacklistReplyNumber({$userID})" class="form-button">
						<img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
						<span class="form-button-text">Blacklist Number</span>
					</a>
				</th>
			</tr>
		</thead>
		<tbody>
			{section name=msisdn loop=$replyBlacklist}
			<tr class="{cycle values="zebra-odd,zebra-even"}">
				<td class="type-phone">{$replyBlacklist[msisdn].replyBlacklistMsisdn}</td>
				<td class="type-action"><a href="#" onclick="$app.module('apiuser').unblacklistReplyNumber({$userID}, '{$replyBlacklist[msisdn].replyBlacklistMsisdn}')" class="form-button"><img src="skin/images/icon-remove.png" class="icon-image icon-size-small" alt="" /></a></td>
			</tr>
			{/section}
		</tbody>
	</table>
</fieldset>