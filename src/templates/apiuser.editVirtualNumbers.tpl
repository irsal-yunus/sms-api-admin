<fieldset>
	<table class="admin-simpletable">
		<thead>
			<tr>
				<th class="zebra-odd">Destination</th>
				<th class="zebra-even">Forward URL</th>
				<th class="zebra-odd">Forward Status</th>
				<th class="zebra-even">
					<a href="#" onclick="$app.module('apiuser').addVirtualNumber({$userID})" class="form-button">
						<img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
						<span class="form-button-text">Add New</span>
					</a>
				</th>
			</tr>
		</thead>
		<tbody>
			{section name=vnum loop=$virtualNumber}
			<tr class="{cycle values="zebra-odd,zebra-even"}">
				<td class="type-phone">{$virtualNumber[vnum].virtualDestination}</td>
				<td class="type-url">{$virtualNumber[vnum].virtualUrl}</td>
				<td class="type-status">{$virtualNumber[vnum].virtualUrlStatusName}</td>
				<td class="type-action">
					<a href="#" onclick="$app.module('apiuser').editVirtualNumber({$virtualNumber[vnum].virtualNumberID}, {$userID})" class="form-button"><img src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" /></a>
					<a href="#" onclick="$app.module('apiuser').removeVirtualNumber({$virtualNumber[vnum].virtualNumberID}, {$userID})" class="form-button"><img src="skin/images/icon-remove.png" class="icon-image icon-size-small" alt="" /></a>
				</td>
			</tr>
			{/section}
		</tbody>
	</table>
</fieldset>