<fieldset> 
	<dl class="admin-definitions">
		<dt>Username</dt><dd>{$details.userName}</dd>
		<dt>Client Company</dt><dd><a href="#" onclick="$app.module('client').viewClient({$details.clientID})">{$details.clientCompanyName}</a></dd>
		<dt>Cobrander ID (User)</dt><dd>{$details.cobranderID}</dd>
		<dt>Status</dt><dd>{$details.statusName}</dd>
		<dt>Status Delivery</dt><dd>{$details.statusDeliveryStatusName}</dd>
		<dt>Delivery URL</dt><dd>{$details.statusDeliveryUrl}</dd>
		<dt>Delivery Failed</dt><dd>{$details.statusDeliveryUrlInvalidCount} time(s)</dd>
		<dt>Delivery Retry</dt><dd>{$details.statusDeliveryUrlLastRetry}</dd>
		<dt>Reply Blacklist</dt><dd>{$details.replyBlacklistStatusName}</dd>
		<dt>Postpaid</dt><dd>{$details.isPostpaidStatusName}</dd>
		<dt>Last Access</dt><dd>{$details.lastAccess}</dd>
		<dt>Created By</dt><dd>{$details.createdByName}</dd>
		<dt>Created On</dt><dd>{$details.createdTimestamp}</dd>
		<dt>Updated By</dt><dd>{$details.updatedByName}</dd>
		<dt>Updated On</dt><dd>{$details.updatedTimestamp}</dd>
		<dt>Expired On</dt><dd>{$details.expiredDate}</dd>
	</dl>
</fieldset>
<fieldset class="form-fieldset-submission">
	<a href="#" title="View Client" class="form-button" onclick="$app.module('client').viewClient({$details.clientID});">
		<img src="skin/images/icon-client.png" class="form-button-image" alt="" />
		<span class="form-button-text">View Client</span>
	</a>
	<a href="#" title="Edit User" class="form-button" onclick="$app.module('apiuser').editAccountDetails({$userID});">
		<img title="Register" src="skin/images/icon-edit.png" class="form-button-image" alt="" />
		<span class="form-button-text">Change Details</span>
	</a>
	{if {$details.active}}
	<a href="#" class="form-button" onclick="$app.module('apiuser').deactivateUser({$userID});">
		<img title="Deactivate" src="skin/images/icon-disable.png" class="form-button-image" alt="" />
		<span class="form-button-text">Deactivate</span>
	</a>
	{else}
	<a href="#" class="form-button" onclick="$app.module('apiuser').activateUser({$userID});">
		<img title="Activate" src="skin/images/icon-enable.png" class="form-button-image" alt="" />
		<span class="form-button-text">Activate</span>
	</a>
	{/if}
	<a href="#" class="form-button" onclick="$app.module('apiuser').changePassword({$userID});">
		<img title="Change Password" src="skin/images/icon-password.png" class="form-button-image" alt="" />
		<span class="form-button-text">Change Password</span>
	</a>
</fieldset>