<form id="apiuser-editform" class="admin-tabform" action="#" method="post" style="width: 100%;">
	<input type="hidden" value="{$userID}" />
	<div id="apiuser-editform-tabs" class="panel-tabs">
		<ul>
			<li><a href="{$smarty.const.SMSAPIADMIN_SERVICE_URL}apiuser.editAccountOverview.php?userID={$userID}"><img src="skin/images/icon-user.png" class="icon-image icon-size-small" alt="" /><span>Account</span></a></li>
			<li><a href="{$smarty.const.SMSAPIADMIN_SERVICE_URL}apiuser.editSenders.php?userID={$userID}"><img src="skin/images/icon-senderid.png" class="icon-image icon-size-small" alt="" /><span>Sender ID</span></a></li>
			<li><a href="{$smarty.const.SMSAPIADMIN_SERVICE_URL}apiuser.editIP.php?userID={$userID}"><img src="skin/images/icon-ip.png" class="icon-image icon-size-small" alt="" /><span>IP Restrictions</span></a></li>
			<li><a href="{$smarty.const.SMSAPIADMIN_SERVICE_URL}apiuser.editVirtualNumbers.php?userID={$userID}"><img src="skin/images/icon-virtualnumber.png" class="icon-image icon-size-small" alt="" /><span>Virtual Numbers</span></a></li>
{*
			<li><a href="{$smarty.const.SMSAPIADMIN_SERVICE_URL}apiuser.editReplyBlacklist.php?userID={$userID}"><img src="skin/images/icon-blacklist.png" class="icon-image icon-size-small" alt="" /><span>Reply Blacklist</span></a></li>
*}
		</ul>		
	</div><!-- END Tabs Container -->
</form>