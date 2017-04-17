<fieldset>
	<table class="admin-simpletable">
		<thead>
			<tr>
				<th class="zebra-odd" style="width:20%;">Sender name</th>
				<th class="zebra-even" style="width:20%;">Range Start</th>
				<th class="zebra-odd" style="width:20%;">Range End</th>
                                <th class="zebra-even" style="width: 10%">Cobrander ID</th>
				<th class="zebra-odd" style="width:10%;">Status</th>           
				<th class="zebra-even" style="width:20%;">
					<a href="#" onclick="$app.module('apiuser').addSender({$userID})" class="form-button">
						<img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
						<span class="form-button-text">Add Sender</span>
					</a>
				</th>
			</tr>
		</thead>
		<tbody>
			{section name=sender loop=$senderID}
			<tr class="{cycle values="zebra-odd,zebra-even"}">
				<td class="type-text">{$senderID[sender].senderName}</td>
				<td class="type-phone">{$senderID[sender].rangeStart}</td>
				<td class="type-phone">{$senderID[sender].rangeEnd}</td>
                                <td class="type-text">{$senderID[sender].cobranderId}</td>
				<td class="type-status">{$senderID[sender].senderStatusName}</td>
				<td class="type-action">
					{if $senderID[sender].senderEnabled}
					<a href="#" onclick="$app.module('apiuser').disableSender({$senderID[sender].senderID}, {$userID});" class="form-button"><img title="Disable" src="skin/images/icon-disable.png" class="icon-image icon-size-small" alt="" /></a>
					{else}
					<a href="#" onclick="$app.module('apiuser').enableSender({$senderID[sender].senderID}, {$userID});" class="form-button"><img title="Enable" src="skin/images/icon-enable.png" class="icon-image icon-size-small" alt="" /></a>
					{/if}
					<a href="#" onclick="$app.module('apiuser').editSender({$senderID[sender].senderID}, {$userID});" class="form-button"><img title="Edit" src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" /></a>
				</td>
			</tr>
			{/section}
		</tbody>
	</table>
</fieldset>