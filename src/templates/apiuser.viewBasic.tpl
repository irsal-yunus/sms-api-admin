<fieldset>  
<dl class="admin-definitions">
	<dt>Username</dt><dd>{$details.userName}</dd>
	<dt>Client Company</dt><dd><a href="#">{$details.clientCompanyName}</a></dd>
	<dt>Cobrander ID</dt><dd>{$details.cobranderID}</dd>
	<dt>Status</dt><dd>{$details.statusName}</dd>
	<dt>Status Delivery</dt><dd>{$details.statusDeliveryStatusName}</dd>
	<dt>Delivery URL</dt><dd>{$details.statusDeliveryUrl}</dd>
	<dt>Delivery Failed</dt><dd>{$details.statusDeliveryUrlInvalidCount} time(s)</dd>
	<dt>Delivery Retry</dt><dd>{$details.statusDeliveryUrlLastRetry}</dd>
	<dt>Reply Blacklist</dt><dd>{$details.replyBlacklistStatusName}</dd>
	<dt>Is Postpaid</dt><dd>{$details.isPostpaidStatusName}</dd>
	<dt>Last Access</dt><dd>{$details.lastAccess}</dd>
	<dt>Created By</dt><dd>{$details.createdByName}</dd>
	<dt>Created On</dt><dd>{$details.createdTimestamp}</dd>
	<dt>Updated By</dt><dd>{$details.updatedByName}</dd>
	<dt>Updated On</dt><dd>{$details.updatedTimestamp}</dd>
        <dt>Expired On</dt><dd>{$details.expiredDate}</dd>
</dl>
</fieldset>