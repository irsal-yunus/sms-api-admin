<button onclick="$app.module('client').topFunction();" id="backToTop" class="form-button back-to-top" title="Back to top"><img src="skin/images/icon-backtotop.png" class="icon-backtotop" alt="Back to top"/></button>
<table class="admin-table">
	<thead>
		<tr>
			<th class="type-nav" colspan="5">
				{if isset($options.onlyUnarchived) && $options.onlyUnarchived}
					<a href="#" style="float:left;" class="form-button" onclick="var arg = $.extend({}, {htmlentities($optionsJson)}, { onlyUnarchived:false}); $app.module('client').showClientList(arg);">
						<span class="form-button-text"> Include Archived Client</span>
					</a>
				{else}
					<a href="#" style="float:left;" class="form-button" onclick="var arg = $.extend({}, {htmlentities($optionsJson)}, { onlyUnarchived:true}); $app.module('client').showClientList(arg);">
						<span class="form-button-text"> Show only non-archived Client</span>
					</a>
				{/if}
				<a href="#" style="float:right;" class="form-button" onclick="$app.module('client').createNew();">
					<img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
					<span class="form-button-text">Register</span>
				</a>
			</th>
		</tr>
		<tr>
			<th style="width: 20%;">Company Name</th>
			<th style="width: 20%;">Country</th>
			<th style="width: 10%;">Contact Name</th>
			<th style="width: 10%;">Contact Phone</th>
			<th style="width: 40%;">Action</th>
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
				{if {$clients[list].archivedDate} eq NULL}
					<a href="#" title="Archived" class="form-button" onclick="$app.module('client').archived({$clients[list].clientID},{$clients[list].archivedDate});"><img src="skin/images/archived.png" class="icon-image" alt="" />
					</a>
				{else}
					<a href="#" title="un Archived" class="form-button" onclick="$app.module('client').archived({$clients[list].clientID},{$clients[list].archivedDate});"><img src="skin/images/unarchived.png" class="icon-image" alt="" /></a>
				{/if}
				<a href="#" title="View" class="form-button" onclick="$app.module('client').viewClient({$clients[list].clientID});"><img src="skin/images/icon-view.png" class="icon-image" alt="" /></a>
				<a href="#" title="Edit" class="form-button" onclick="$app.module('client').editClient({$clients[list].clientID});"><img src="skin/images/icon-edit.png" class="icon-image" alt="" /></a>
				<a href="#" title="Delete" class="form-button" onclick="$app.module('client').removeClient({$clients[list].clientID});"><img src="skin/images/icon-remove.png" class="icon-image" alt="" /></a>
				<a href="#" title="Manage Users" class="form-button" onclick="$app.module('client').manageUsers({$clients[list].clientID});"><img src="skin/images/icon-user.png" class="icon-image" alt="" /></a>
			</td>
		</tr>
		{/section}
	</tbody>
</table>
