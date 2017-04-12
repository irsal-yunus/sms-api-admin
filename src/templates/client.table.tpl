<table class="admin-table">
	<thead>
		<tr>
			<th style="width: 20%;">Company Name</th>
			<th style="width: 15%;">Country</th>
			<th style="width: 20%;">Contact Name</th>
			<th style="width: 15%;">Contact Phone</th>
			<th style="width: 30%;">
				<a href="#" class="form-button" onclick="$app.module('client').createNew();">
					<img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
					<span class="form-button-text">Register</span>
				</a>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="9">&nbsp;</th>
		</tr>
	</tfoot>
	<tbody>
		{section name=list loop=$clients}
		<tr class="{cycle values="zebra-odd,zebra-even"}{if $clients[list].clientID==$options.highlight} table-row-highlight{/if}">
			<td class="type-text">{$clients[list].companyName}</td>
			<td class="type-text">{$clients[list].countryName}</td>
			<td class="type-text"><a href="mailto:{$clients[list].contactEmail}">{$clients[list].contactName}</a></td>
			<td class="type-phone">{$clients[list].contactPhone}</td>
			<td class="type-action">
				<a href="#" title="View" class="form-button" onclick="$app.module('client').viewClient({$clients[list].clientID});"><img src="skin/images/icon-view.png" class="icon-image" alt="" /></a>
				<a href="#" title="Edit" class="form-button" onclick="$app.module('client').editClient({$clients[list].clientID});"><img src="skin/images/icon-edit.png" class="icon-image" alt="" /></a>
				<a href="#" title="Delete" class="form-button" onclick="$app.module('client').removeClient({$clients[list].clientID});"><img src="skin/images/icon-remove.png" class="icon-image" alt="" /></a>
				<a href="#" title="Manage Users" class="form-button" onclick="$app.module('client').manageUsers({$clients[list].clientID});"><img src="skin/images/icon-user.png" class="icon-image" alt="" /></a>
                                <a href="#" title="Billing Options" class="form-button" onclick="$app.module('client').smsBilling({$clients[list].clientID});"><img src="skin/images/icon-client.png" class="icon-image" alt="" /></a>
			</td>
		</tr>
		{/section}
	</tbody>
</table>
