<form action="#" class="admin-stealthform">
<table id="apiuser-simpletable" class="admin-table">
	{if $options.onlySpecifiedClient}
	<caption>API Users of Client "<strong>{$client.companyName}</strong>"</caption>
	{/if}
	<thead>
		<tr>
			<th class="type-nav" colspan="5">
				{if isset($options.onlyActiveUser) && $options.onlyActiveUser}
				<a href="#" class="form-button" onclick="var arg = $.extend({}, {htmlentities($optionsJson)}, { onlyActiveUser:false}); $app.module('apiuser').showUserList(arg);">
					<span class="form-button-text">Include Inactive User</span>
				</a>
				{else}
				<a href="#" class="form-button" onclick="var arg = $.extend({}, {htmlentities($optionsJson)}, { onlyActiveUser:true}); $app.module('apiuser').showUserList(arg);">
					<span class="form-button-text">Show Only Active User</span>
				</a>
				{/if}
			</th>
		</tr>
		<tr>
			<th style="width: 20%;">Account Name</th>
			<th style="width: 20%;">Client Name</th>
			<th style="width: 10%;">Balance</th>
			<th style="width: 10%;">Status</th>
			<th style="width: 40%;">
					{if $options.onlySpecifiedClient}
					<a href="#" class="form-button" onclick="$app.module('apiuser').createUser({$options.clientID});">
						<img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
					<span class="form-button-text">Register</span>
					{else}
					<a href="#" class="form-button" onclick="$app.module('apiuser').createUser();">
						<img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
					<span class="form-button-text">Register</span>
					{/if}
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
		<tr class="{cycle values="zebra-odd,zebra-even"}{if $users[list].userID==$options.highlight} table-row-highlight{/if}">
			<td class="type-text">{$users[list].userName}</td>
			<td class="type-text">{$users[list].clientCompanyName}</td>
			<td class="type-counter">{$users[list].userCredit}</td>
			<td class="type-status">{$users[list].statusName}</td>
			<td class="type-action">
				{if {$users[list].active}}
				<a href="#" title="Deactivate" onclick="$app.module('apiuser').deactivateUser({$users[list].userID}, true);" class="form-button"><img src="skin/images/icon-disable.png" class="icon-image" alt="" /></a>
				{else}
				<a href="#" title="Activate" onclick="$app.module('apiuser').activateUser({$users[list].userID}, true);" class="form-button"><img src="skin/images/icon-enable.png" class="icon-image" alt="" /></a>
				{/if}
				<a href="#" title="View Details" class="form-button" onclick="$app.module('apiuser').showUserDetails({$users[list].userID});"><img src="skin/images/icon-view.png" class="icon-image" alt="" /></a>
				<a href="#" title="Edit" class="form-button" onclick="$app.module('apiuser').editUser({$users[list].userID});"><img src="skin/images/icon-edit.png" class="icon-image" alt="" /></a>
				<a href="#" title="Manage Credit" class="form-button" onclick="$app.module('credit').manageUserCredit({$users[list].userID});"><img src="skin/images/icon-credit.png" class="icon-image" alt="" /></a>
			</td>
		</tr>
		{/section}
	</tbody>
</table>
</form>