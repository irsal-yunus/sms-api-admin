<form action="#" class="admin-stealthform">
<table class="admin-table">
	<thead>
		<tr>
			<th style="width: 20%;">Account Name</th>
			<th style="width: 10%;">Balance</th>
			<th style="width: 10%;">Status</th>
			<th style="width: 40%;">
				<a href="#" title="Register" class="form-button" onclick="$app.module('apiuser').createUser({$clientID});">
					<img src="skin/images/icon-add.png" class="form-button-image" alt="" />
					<span class="form-button-text">Register</span>
				</a>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="9">
				&nbsp;
			</th>
		</tr>
	</tfoot>
	<tbody>
		{section name=list loop=$users}
		<tr class="{cycle values="zebra-odd,zebra-even"}">
			<td class="type-text">{$users[list].userName}</td>
			<td class="type-counter">{$users[list].userCredit}</td>
			<td class="type-status">{$users[list].statusName}</td>
			<td class="type-action">
				{if {$users[list].active}}
				<a href="#" onclick="$app.module('apiuser').deactivateUser({$users[list].userID});" class="form-button"><img title="Deactivate" src="skin/images/icon-disable.png" class="icon-image icon-size-small" alt="" /></a>
				{else}
				<a href="#" onclick="$app.module('apiuser').activateUser({$users[list].userID});" class="form-button"><img title="Activate" src="skin/images/icon-enable.png" class="icon-image icon-size-small" alt="" /></a>
				{/if}
				<div class="form-button" onclick="$app.module('apiuser').showUserDetails({$users[list].userID});"><img title="View" src="skin/images/icon-view.png" class="icon-image icon-size-small" alt="" /></div>
				<div class="form-button" onclick="$app.module('apiuser').editUser({$users[list].userID});"><img title="Edit" src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" /></div>
				<div class="form-button" onclick="$app.module('credit').manage({$users[list].userID});"><img title="Manage Credit" src="skin/images/icon-credit.png" class="icon-image icon-size-small" alt="" /></div>
			</td>
		</tr>
		{/section}
	</tbody>
</table>
</form>