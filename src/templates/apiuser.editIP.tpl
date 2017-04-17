<fieldset>
	<table class="admin-simpletable">
		<thead>
			<tr>
				<th class="zebra-odd" style="width:65%;">Permitted IP</th>
				<th class="zebra-even" style="width:35%;">
					<a href="#" onclick="$app.module('apiuser').allowIP({$userID})" class="form-button">
						<img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
						<span class="form-button-text">Allow IP</span>
					</a>
				</th>
			</tr>
		</thead>
		<tbody>
			{section name=ip loop=$permittedIP}
			<tr class="{cycle values="zebra-odd,zebra-even"}">
				<td class="type-ip">{$permittedIP[ip].ipAddress}</td>
				<td class="type-action"><a href="#" onclick="$app.module('apiuser').disallowIP({$userID}, '{$permittedIP[ip].ipAddress}')" class="form-button"><img src="skin/images/icon-remove.png" class="icon-image icon-size-small" alt="" /></a></td>
			</tr>
			{/section}
		</tbody>
	</table>
</fieldset>