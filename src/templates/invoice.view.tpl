<form id="invoice-view" class="admin-tabform" action="#" method="post" style="width: 100%;">
	<input type="hidden" value="{$userID}" />
	<div id="invoice-view-tabs" class="panel-tabs">
		<ul>
			<li><a href="{$smarty.const.SMSAPIADMIN_SERVICE_URL}invoice.profile.php"><img src="skin/images/icon-profile.png" class="icon-image icon-size-small" alt="" /><span>Invoice Profile</span></a></li>
			<li><a href="{$smarty.const.SMSAPIADMIN_SERVICE_URL}invoice.setting.php"><img src="skin/images/icon-setting.png" class="icon-image icon-size-small" alt="" /><span>Settings</span></a></li>
		</ul>
	</div><!-- END Tabs Container -->
</form>
