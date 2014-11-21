<form id="apiuser-viewform" class="admin-tabform" action="#" method="post" style="width: 100%;">
	 <input type="hidden" value="{$details.userID}" />
	<div id="apiuser-viewform-tabs" class="panel-tabs">
		<ul>
			<li><a href="#apiuser-viewform-tab-account"><img src="skin/images/icon-user.png" class="icon-image icon-size-small" alt="" /><span>Account</span></a></li>
			<li><a href="#apiuser-viewform-tab-senderid"><img src="skin/images/icon-senderid.png" class="icon-image icon-size-small" alt="" /><span>Sender ID</span></a></li>
			<li><a href="#apiuser-viewform-tab-ip"><img src="skin/images/icon-ip.png" class="icon-image icon-size-small" alt="" /><span>IP Restrictions</span></a></li>
			<li><a href="#apiuser-viewform-tab-reply"><img src="skin/images/icon-virtualnumber.png" class="icon-image icon-size-small" alt="" /><span>Virtual Numbers</span></a></li>
{*
			<li><a href="#apiuser-viewform-tab-replyblacklist"><img src="skin/images/icon-blacklist.png" class="icon-image icon-size-small" alt="" /><span>Reply Blacklist</span></a></li>
*}
		</ul>
		<div id="apiuser-viewform-tab-account">
			<dl class="admin-definitions">
				<dt>Username</dt><dd>{$details.userName}</dd>
				<dt>Client Company</dt><dd><a href="#">{$details.clientCompanyName}</a></dd>
				<dt>Cobrander ID (User)</dt><dd>{$details.cobranderID}</dd>
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
			<fieldset class="form-fieldset-submission">
				<a href="#" title="View Client" class="form-button" onclick="$app.module('client').viewClient({$details.clientID});;">
					<img src="skin/images/icon-client.png" class="form-button-image" alt="" />
					<span class="form-button-text">View Client</span>
				</a>
				<a href="#" title="Edit Mode" class="form-button" onclick="$app.module('apiuser').editUser({$details.userID});">
					<img src="skin/images/icon-edit.png" class="form-button-image" alt="" />
					<span class="form-button-text">Edit Mode</span>
				</a>
			</fieldset>
		</div><!-- END Account tab-->
		<div id="apiuser-viewform-tab-senderid">
			<table class="admin-simpletable">
				<thead>
					<tr>
						<th class="zebra-odd">Sender Name</th>
						<th class="zebra-even">Range Start</th>
						<th class="zebra-odd">Range End</th>
                                                <th class="zebra-even">CobranderId</th>
						<th class="zebra-odd">Status</th>
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
					</tr>
					{/section}
				</tbody>
			</table>
		</div><!-- END SenderID tab-->
		<div id="apiuser-viewform-tab-ip">
			<table class="admin-simpletable">
				<thead>
					<tr>
						<th class="zebra-odd">Permitted IP</th>
					</tr>
				</thead>
				<tbody>
					{section name=ip loop=$permittedIP}
					<tr class="{cycle values="zebra-odd,zebra-even"}">
						<td class="type-ip">{$permittedIP[ip].ipAddress}</td>
					</tr>
					{/section}
				</tbody>
			</table>
		</div><!-- END IP tab-->
		<div id="apiuser-viewform-tab-reply">
			<table class="admin-simpletable">
				<thead>
					<tr>
						<th class="zebra-odd">Destination</th>
						<th class="zebra-even">URL Active</th>
						<th class="zebra-odd">Forward URL</th>
						<th class="zebra-even">Invalid Forward</th>
						<th class="zebra-odd">Last Forwaring  Retry</th>
					</tr>
				</thead>
				<tbody>
					{section name=vnumber loop=$virtualNumber}
					<tr class="{cycle values="zebra-odd,zebra-even"}">
						<td class="type-text">{$virtualNumber[vnumber].virtualDestination}</td>
						<td class="type-url">{$virtualNumber[vnumber].virtualUrl}</td>
						<td class="type-status">{$virtualNumber[vnumber].virtualUrlStatusName}</td>
						<td class="type-counter">{$virtualNumber[vnumber].virtualUrlInvalidCount}</td>
						<td class="type-date">{$virtualNumber[vnumber].virtualUrlLastRetry}</td>
					</tr>
					{/section}
				</tbody>
			</table>
		</div><!-- END Reply tab-->
{*
		<div id="apiuser-viewform-tab-replyblacklist">
			<table class="admin-simpletable">
				<thead>
					<tr>
						<th class="zebra-odd">Blacklisted MSISDN</th>
					</tr>
				</thead>
				<tbody>
					{section name=msisdn loop=$replyBlacklist}
					<tr class="{cycle values="zebra-odd,zebra-even"}">
						<td class="type-phone">{$replyBlacklist[msisdn].replyBlacklistMsisdn}</td>
					</tr>
					{/section}
				</tbody>
			</table>
		</div><!-- END Reply Blacklist tab-->
*}

	</div><!-- END Tabs Container -->
</form>