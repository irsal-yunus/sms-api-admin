<form id="clients-view-form" class="admin-tabform" action="#">
	<div class="panel">
		<div class="panel-header"><img src="skin/images/icon-client.png" class="icon-image" alt="" /><span>Client Details</span></div>
		<div class="panel-body">
			<div class="panel-content">
				<fieldset class="float-centre">
					<dl class="admin-definitions">
						<dt>Company Name</dt><dd>{$client.companyName}</dd>
						<dt>Company URL</dt><dd><a href="{$client.companyUrl}">{$client.companyUrl}</a></dd>
						<dt>Country</dt><dd>{$client.countryName}</dd>
						<dt>Contact Name</dt><dd>{$client.contactName}</dd>
						<dt>Contact Email</dt><dd><a href="mailto:{$clientcontactEmail}">{$client.contactEmail}</a></dd>
						<dt>Contact Phone</dt><dd>{$client.contactPhone}</dd>
						<dt>Created By</dt><dd>{$client.createdByName}</dd>
						<dt>Created On</dt><dd>{$client.createdTimestamp}</dd>
						<dt>Updated By</dt><dd>{$client.updatedByName}</dd>
						<dt>Updated On</dt><dd>{$client.updatedTimestamp}</dd>
					</dl>
				</fieldset>
				<fieldset class="form-fieldset-submission float-centre">
					<a href="#" class="form-button" onclick="$app.module('client').manageUsers({$client.clientID});">
						<img src="skin/images/icon-user.png" class="form-button-image" alt="" />
						<span class="form-button-text">API Accounts</span>
					</a>
					<a href="#" class="form-button" onclick="$app.module('client').editClient({$client.clientID});">
						<img src="skin/images/icon-edit.png" class="form-button-image" alt="" />
						<span class="form-button-text">Edit</span>
					</a>
					<a href="#" class="form-button" onclick="$app.module('client').removeClient({$client.clientID});">
						<img src="skin/images/icon-remove.png" class="form-button-image" alt="" />
						<span class="form-button-text">Delete</span>
					</a>
				</fieldset>
			</div>
		</div>
	</div>
</form>